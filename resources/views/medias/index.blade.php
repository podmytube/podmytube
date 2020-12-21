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
                    @if (isset($media->grabbed_at))
                        <svg class="h-6 w-auto inline fill-current" role="img" aria-labelledby="mediaStatus{{$media->id()}}"
                            xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                            <title id="mediaStatus{{$media->id()}}">Episode "{{$media->title}}" is available in your podcast.</title>
                            <g fill="none" fill-rule="evenodd">
                                <circle cx="10" cy="10" r="10" fill="#9CE2B6"></circle>
                                <polyline stroke="#126D34" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2" points="6 10 8.667 12.667 14 7.333"></polyline>
                            </g>
                        </svg>
                    @else
                        <svg class="h-6 w-auto inline fill-current" role="img" aria-labelledby="mediaStatus{{$media->id()}}"
                            xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                            <title id="mediaStatus{{$media->id()}}">Episode "{{$media->title}}" has not been downloaded yet.</title>
                            <path d="m256 0c-141.164062 0-256 114.835938-256 256s114.835938 256 256 256 256-114.835938 256-256-114.835938-256-256-256zm0 0" fill="#f44336"/>
                            <path d="m350.273438 320.105469c8.339843 8.34375 8.339843 21.824219 0 30.167969-4.160157 4.160156-9.621094 6.25-15.085938 6.25-5.460938 0-10.921875-2.089844-15.082031-6.25l-64.105469-64.109376-64.105469 64.109376c-4.160156 4.160156-9.621093 6.25-15.082031 6.25-5.464844 0-10.925781-2.089844-15.085938-6.25-8.339843-8.34375-8.339843-21.824219 0-30.167969l64.109376-64.105469-64.109376-64.105469c-8.339843-8.34375-8.339843-21.824219 0-30.167969 8.34375-8.339843 21.824219-8.339843 30.167969 0l64.105469 64.109376 64.105469-64.109376c8.34375-8.339843 21.824219-8.339843 30.167969 0 8.339843 8.34375 8.339843 21.824219 0 30.167969l-64.109376 64.105469zm0 0" fill="#fafafa"/>
                        </svg>
                    @endif
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