@extends('layouts.cockpit')

@section('pageTitle', 'Podmytube cockpit.')

@section('content')

    <div class="max-w-screen-xl mx-auto mt-4 mb-16">
        <div class="flex flex-col sm:flex-row sm:justify-between">
            @if ($lastRegisteredChannel)
                <div class="mt-2 py-2 px-4 rounded border bg-gray-800 text-gray-100">
                    <h4 class="h4">last channel</h4>
                    <span class="h3">{{ $lastRegisteredChannel->title() }}</span>
                    ({{ $lastRegisteredChannel->subscription->plan->name }})
                    <p class="text-center">
                        <a href="{{ $lastRegisteredChannel->podcastUrl() }}">podcast</a>
                        <a href="{{ $lastRegisteredChannel->youtubeUrl() }}">channel</a>
                    </p>
                </div>
            @endif

            <div class="mt-2 py-2 px-4 rounded border bg-gray-800 text-gray-100">
                <h3 class="h3">feeds</h3>
                <p class="text-center">{{ $nbActiveChannels }} / {{ $nbPodcasts }}</p>
            </div>

            <div class="mt-2 py-2 px-4 rounded border bg-gray-800 text-gray-100">
                <h3 class="h3">medias</h3>
                <p class="text-center">{{ $nbMedias }}</p>
            </div>

            <div class="mt-2 py-2 px-4 rounded border bg-gray-800 text-gray-100">
                <h3 class="h3">revenues</h3>
                <p class="text-center">{{ $revenues }} &euro;</p>
            </div>

            <div class="mt-2 py-2 px-4 rounded border bg-gray-800 text-gray-100">
                <h3 class="h3">volume</h3>
                <p class="text-center">{{ $volumeOnDisk }}</p>
            </div>
        </div>
    </div>


@endsection
