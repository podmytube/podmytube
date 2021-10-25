@extends('layouts.cockpit')

@section('pageTitle', 'Podmytube cockpit.')

@section('content')

    <div class="max-w-screen-xl mx-auto text-gray-100 py-12 px-4">
        <div class="flex flex-col sm:flex-row sm:items-start">
            <div class="p-4 rounded border border-grey-dark">
                <h4 class="h4">last registered channel</h4>
                <span class="h3">{{ $lastRegisteredChannel->title() }}</span>
                ({{ $lastRegisteredChannel->subscription->plan->name }})
                <p>
                    <a href="{{ $lastRegisteredChannel->podcastUrl() }}">podcast</a>
                    <a href="{{ $lastRegisteredChannel->youtubeUrl() }}">channel</a>
                </p>
            </div>

            <div class="m-2 p-4 rounded border border-grey-dark">
                <h3 class="h3">nb active podcasts</h3>
                <p>{{ $nbActiveChannels }}</p>
            </div>
        </div>
    </div>


@endsection
