<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Events\MediaUploadedByUserEvent;
use App\Events\PodcastUpdatedEvent;
use App\Http\Requests\MediaRequest;
use App\Http\Requests\UpdateMediaRequest;
use App\Models\Channel;
use App\Models\Media;
use App\Modules\MediaProperties;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Throwable;

class MediasController extends Controller
{
    public const NB_ITEMS_PER_PAGE = 30;
    protected const LOG_PREFIX = 'MediaUploadedByUser';

    public function index(Request $request, Channel $channel)
    {
        $this->authorize('view', $channel);

        try {
            $nbItemsPerPage = $request->query('nb') ?? self::NB_ITEMS_PER_PAGE;

            $medias = Media::query()
                ->withTrashed()
                ->where('channel_id', '=', $channel->channel_id)
                ->orderBy('published_at', 'desc')
                ->simplePaginate($nbItemsPerPage)
            ;

            return view('medias.index', compact('channel', 'medias', 'nbItemsPerPage'));
        } catch (Throwable $exception) {
            Log::error($exception->getMessage());

            return redirect()->route('home')->withErrors(['danger' => $exception->getMessage()]);
        }
    }

    public function create(Channel $channel)
    {
        $this->authorize('addMedia', $channel);

        try {
            $pageTitle = 'Add an exclusive episode to your podcast.';
            $media = new Media();
            $patch = false;

            return view('medias.createOrEdit', compact('pageTitle', 'media', 'channel', 'patch'));
        } catch (Throwable $exception) {
            Log::error($exception->getMessage());

            return redirect()->route('home')->withErrors(['danger' => $exception->getMessage()]);
        }
    }

    public function edit(Channel $channel, Media $media)
    {
        $this->authorize('addMedia', $channel);

        try {
            $pageTitle = "Edit {$media->name} episode ";
            $patch = true;

            return view('medias.createOrEdit', compact('pageTitle', 'media', 'channel', 'patch'));
        } catch (Throwable $exception) {
            Log::error($exception->getMessage());

            return redirect()->route('home')->withErrors(['danger' => $exception->getMessage()]);
        }
    }

    public function store(MediaRequest $request, Channel $channel)
    {
        $this->authorize('addMedia', $channel);

        try {
            $validatedParams = $request->validated();

            Log::debug(self::LOG_PREFIX . " {$channel->nameWithId()} : form is validated.");

            /** analyze the audio file */
            $mediaProperties = MediaProperties::analyzeFile($request->file('media_file'));

            Log::debug(self::LOG_PREFIX . " {$channel->nameWithId()} : media has been analyzed.");

            /** getting media_id */
            $mediaId = $channel->nextMediaId();

            // moving file where we can find it
            $path = Storage::putFileAs('uploadedMedias', $request->file('media_file'), $mediaId . '.mp3');

            Log::debug(self::LOG_PREFIX . " {$channel->nameWithId()} : file has been moved to {$path}.");

            /** save the information */
            $media = Media::create([
                'channel_id' => $channel->channel_id,
                'media_id' => $mediaId,
                'title' => $validatedParams['title'],
                'description' => $validatedParams['description'],
                'duration' => $mediaProperties->duration(),
                'length' => $mediaProperties->filesize(),
                'uploaded_by_user' => true,
                'published_at' => Carbon::now(),
                'grabbed_at' => Carbon::now(),
                'status' => Media::STATUS_UPLOADED_BY_USER,
            ]);

            Log::debug(self::LOG_PREFIX . " {$channel->nameWithId()} : media is persisted with id {$media->id}.");

            // dispatching event
            MediaUploadedByUserEvent::dispatch($media);

            Log::debug(self::LOG_PREFIX . " {$channel->nameWithId()} : media has been dispatched for upload.");

            return redirect()
                ->route('channel.medias.index', $channel)
                ->with('success', "Your episode {$validatedParams['title']} has been successfully added.")
            ;
        } catch (Throwable $exception) {
            Log::error($exception->getMessage());

            return redirect()->route('home')->withErrors(['danger' => $exception->getMessage()]);
        }
    }

    public function update(UpdateMediaRequest $request, Channel $channel, Media $media)
    {
        $this->authorize('addMedia', $channel);

        try {
            $validatedParams = $request->validated();

            $updateMediaParams = [
                'title' => $validatedParams['title'],
                'description' => $validatedParams['description'],
                'uploaded_by_user' => true,
                'published_at' => Carbon::now(),
                'grabbed_at' => Carbon::now(),
                'status' => Media::STATUS_UPLOADED_BY_USER,
            ];

            if ($request->file('media_file') !== null) {
                Log::debug(self::LOG_PREFIX . " {$channel->nameWithId()} : media has been uploaded again.");

                /** analyze the audio file */
                $mediaProperties = MediaProperties::analyzeFile($request->file('media_file'));

                Log::debug(self::LOG_PREFIX . " {$channel->nameWithId()} : media has been analyzed.");

                /** getting media_id */
                $mediaId = $channel->nextMediaId();

                $updateMediaParams = [
                    'media_id' => $mediaId,
                    'duration' => $mediaProperties->duration(),
                    'length' => $mediaProperties->filesize(),
                ];

                // moving file where we can find it
                $path = Storage::putFileAs('uploadedMedias', $request->file('media_file'), $mediaId . '.mp3');

                Log::debug(self::LOG_PREFIX . " {$channel->nameWithId()} : file has been moved to {$path}.");
            }

            // save the information
            $media->update($updateMediaParams);

            Log::debug(self::LOG_PREFIX . " {$channel->nameWithId()} : media has been updated in db.");

            // dispatching event
            MediaUploadedByUserEvent::dispatch($media);

            Log::debug(self::LOG_PREFIX . " {$channel->nameWithId()} : media has been dispatched.");

            return redirect()
                ->route('channel.medias.index', $channel)
                ->with('success', "Your episode {$validatedParams['title']} has been successfully updated.")
            ;
        } catch (Throwable $exception) {
            Log::error($exception->getMessage());

            return redirect()->route('home')->withErrors(['danger' => $exception->getMessage()]);
        }
    }

    public function disable(Media $media)
    {
        $this->authorize('addMedia', $media->channel);

        try {
            $media->delete();

            PodcastUpdatedEvent::dispatch($media->channel);

            return redirect(route('channel.medias.index', $media->channel))
                ->with(
                    'success',
                    "Your episode {$media->title} won't be published anymore."
                )
            ;
        } catch (Throwable $exception) {
            Log::error($exception->getMessage());

            return redirect()->route('home')->withErrors(['danger' => $exception->getMessage()]);
        }
    }

    /**
     * @param Media $media
     *
     * DO NOT TYPE HINT
     * If you type hint laravel wont find object and you will get 404 not found
     */
    public function enable($media)
    {
        $media = Media::withTrashed()->find($media);

        $this->authorize('addMedia', $media->channel);

        try {
            $media->restore();

            PodcastUpdatedEvent::dispatch($media->channel);

            return redirect(route('channel.medias.index', $media->channel))
                ->with(
                    'success',
                    "Your episode {$media->title} will be restored soon."
                )
            ;
        } catch (Throwable $exception) {
            Log::error($exception->getMessage());

            return redirect()->route('home')->withErrors(['danger' => $exception->getMessage()]);
        }
    }
}
