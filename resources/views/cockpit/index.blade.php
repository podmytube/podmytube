@extends('layouts.cockpit')

@section('pageTitle', 'Podmytube cockpit.')

@section('content')

    <div class="max-w-screen-xl mx-auto mt-4 mb-16">
        <div class="flex flex-col sm:flex-row sm:flex-wrap sm:justify-start">
            @if ($lastRegisteredChannel)
                <div class="mt-2 mx-2 py-2 px-4 rounded border bg-gray-800 text-gray-100">
                    <h4 class="h4">last channel</h4>
                    <span class="h3">{{ $lastRegisteredChannel->title() }}</span>
                    ({{ $lastRegisteredChannel->subscription->plan->name }})
                    <p class="text-center">
                        <a href="{{ $lastRegisteredChannel->podcastUrl() }}">podcast</a>
                        <a href="{{ $lastRegisteredChannel->youtubeUrl() }}">channel</a>
                    </p>
                </div>
            @endif

            <div class="mt-2 mx-2 py-2 px-4 rounded border bg-gray-800 text-gray-100">
                <h3 class="h3">nb podcasts</h3>
                <p class="text-center">{{ $nbActiveChannels }}</p>
            </div>

            <div class="mt-2 mx-2 py-2 px-4 rounded border bg-gray-800 text-gray-100">
                <h3 class="h3">nb medias</h3>
                <p class="text-center">{{ $nbMedias }}</p>
            </div>

            <div class="mt-2 mx-2 py-2 px-4 rounded border bg-gray-800 text-gray-100">
                <h3 class="h3">revenues</h3>
                <p class="text-center">{{ $revenues }} &euro;</p>
            </div>
        </div>
    </div>


@endsection
