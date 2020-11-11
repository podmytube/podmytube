<?php

namespace App\Http\Controllers;

use App\Channel;
use App\Events\MediaAdded;
use App\Http\Requests\MediaRequest;
use App\Media;
use App\Modules\MediaProperties;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

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

    public function create(Channel $channel)
    {
        $this->authorize('addMedia', $channel);

        $pageTitle = 'Add an exclusive episode to your podcast.';
        $media = new Media();
        $action = URL::route('channel.medias.store', $channel);
        $patch = false;
        return view('medias.createOrEdit', compact('pageTitle', 'action', 'media', 'patch'));
    }

    public function store(MediaRequest $request, Channel $channel)
    {
        $this->authorize('addMedia', $channel);

        $validatedParams = $request->validated();

        /** analyze the audio file */
        $mediaProperties = MediaProperties::analyzeFile($request->file('media_file'));

        dd(md5(uniqid(, true)))
        /** moving file where we can find it  */
        $originalFileName = $request->file('media_file')->getClientOriginalName();
        $path = $request->file('media_file')->storeAs('uploadedMedias', Str::slug($originalFileName));

        dd($path);

        /** save the information */
        $media = Media::create([
            'channel_id' => $channel->channel_id,
            'title' => $validatedParams['title'],
            'description' => $validatedParams['description'],
            'duration' => $mediaProperties->duration(),
            'length' => $mediaProperties->filesize(),
        ]);

        /** dispatching event */
        MediaAdded::dispatch($media);

        return redirect()
            ->with('success', 'A brand new episode has been added to your podcast. It should be available soon.')
            ->route('channel.medias.index');
    }
}
