@extends('layouts.app')

@section('pageTitle', $channel->channel_name .' list of episodes' )

@section ('content')

<div class="max-w-screen-xl mx-auto py-12 px-4">
    @if ($medias->count()>0)

    <h2 class="text-3xl md:text-5xl text-white font-semibold">Podcast episodes</h2>
    
    <div class="bg-gray-100 mt-6 px-4 py-4 rounded-lg max-w-screen-lg md:mx-auto">
        <table class="w-full">
            <thead">
                <tr class="bg-gray-900 text-white">
                    <th class="p-3 border-white">Episode title</th>
                    <th class="p-3">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($medias as $media)
                <tr>
                    <td class="px-2 py-2">
                        {{ $media->title }}
                    </td>
                    <td class="text-center">
                        @include('svg.media_status_'.$media->status, [
                            'cssClass' => 'h-6 w-auto inline fill-current',
                            'comment' => $media->statusComment()
                            ])
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        
        <div class="mt-4 px-6">
            {{ $medias->appends(['nb' => $nbItemsPerPage])->links() }}
        </div>
        
    </div>
    @else
    <div class="bg-gray-100 border rounded-lg border-gray-500 text-gray-900 px-4 py-3" role="alert">
        <p class="font-bold">No episodes yet.</p>
        <p class="text-base">Normal if you registered your channel only minutes ago, else you should <a href="mailto:frederick@podmytube.com">contact me</a>.</p>
    </div>
    @endif
</div>
@endsection