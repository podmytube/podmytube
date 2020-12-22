<?php

namespace App\Http\Controllers;

use App\Channel;
use App\Events\MediaUploadedByUser;
use App\Exceptions\NotImplementedException;
use App\Http\Requests\MediaRequest;
use App\Media;
use App\Modules\MediaProperties;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

class MediasController extends Controller
{
    public const NB_ITEMS_PER_PAGE = 30;

    public function index(Request $request, Channel $channel)
    {
        $this->authorize('view', $channel);

        $nbItemsPerPage = $request->query('nb') ?? self::NB_ITEMS_PER_PAGE;

        $medias = $channel
            ->medias()
            ->orderBy('published_at', 'desc')
            ->simplePaginate($nbItemsPerPage);

        return view('medias.index', compact('channel', 'medias', 'nbItemsPerPage'));
    }

    public function show(Channel $channel, Media $media)
    {
        throw new NotImplementedException(__CLASS__ . '::' . __FUNCTION__ . ' is not implemented yet');
    }

    public function create(Channel $channel)
    {
        $this->authorize('addMedia', $channel);

        $pageTitle = 'Add an exclusive episode to your podcast.';
        $media = new Media();
        $action = URL::route('channel.medias.store', $channel);
        $patch = false;
        return view('medias.createOrEdit', compact('pageTitle', 'action', 'media', 'patch'));
    }

    public function edit(Channel $channel, Media $media)
    {
        $this->authorize('addMedia', $channel);

        $pageTitle = "Edit {$media->name} episode ";
        $action = URL::route('channel.medias.update', ['channel' => $channel, 'media' => $media]);
        $patch = true;
        return view('medias.createOrEdit', compact('pageTitle', 'action', 'media', 'patch'));
    }

    public function store(MediaRequest $request, Channel $channel)
    {
        $this->authorize('addMedia', $channel);

        $validatedParams = $request->validated();

        /** analyze the audio file */
        $mediaProperties = MediaProperties::analyzeFile($request->file('media_file'));

        /** getting media_id */
        $mediaId = $channel->nextMediaId();

        /** moving file where we can find it  */
        Storage::putFileAs('uploadedMedias', $request->file('media_file'), $mediaId . '.mp3');

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
        ]);

        /** dispatching event */
        MediaUploadedByUser::dispatch($media);

        return redirect()
            ->route('channel.medias.index', $channel)
            ->with('success', "Your episode {$validatedParams['title']} has been successfully added.");
    }
}
