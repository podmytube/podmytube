@extends('layouts.app')

@section('pageTitle', $pageTitle)

@section('content')
    <div class="max-w-screen-xl mx-auto py-12 px-4">
        <h2 class="text-3xl md:text-5xl text-white font-semibold">{{ $pageTitle }}</h2>

        <p class="text-sm pb-4 text-gray-100">
            This is the place where you can edit your episode informations.
        </p>

        <div class="rounded-lg bg-white text-gray-900 p-4">
            @if ($patch === true)
                <form method="POST" id="add-or-edit-media" enctype="multipart/form-data"
                    action="{{ route('channel.medias.update', ['channel' => $media->channel, 'media' => $media]) }}">
                    @method('PATCH')
                @else
                    <form method="POST" id="add-or-edit-media" enctype="multipart/form-data"
                        action="{{ route('channel.medias.store', $channel) }}">
            @endif
            @csrf


            <div class="pb-4">
                <label class="block py-1" for="title">Episode title</label>
                <input type="text" id="title" name="title" placeholder="My full interview of Yoda"
                    aria-label="Episode title" class="w-full px-5 py-1 text-gray-900 bg-gray-200 rounded placeholder"
                    value="{{ old('title') ?? $media->title }}">
            </div>

            <div class="pb-4">
                <label class="block py-1" for="description">Episode description</label>
                <textarea id="description" name="description" aria-label="Episode description"
                    class="w-full px-5 py-1 text-gray-900 bg-gray-200 rounded h-96 ">{{ old('description') ?? $media->description }}</textarea>
            </div>

            <div class="pb-4">
                <label class="block py-1" for="media_file">
                    Episode file
                    @if ($patch === true)
                        <small>If you set a new audio file, it will replace the current audio file.</small>
                    @endif
                </label>
                <input type="file" id="media_file" name="media_file" placeholder="My full interview of Yoda"
                    aria-label="Episode file" class="w-full px-5 py-1 text-gray-900 bg-gray-200 rounded placeholder">

            </div>

            <div class="flex justify-center items-center">
                <a href="#" onclick="event.preventDefault(); document.getElementById('add-or-edit-media').submit();">
                    <button type="button"
                        class="flex-1 bg-gray-800 text-gray-100 hover:bg-gray-700 font-bold py-2 px-4 rounded-l-lg">
                        @if ($patch === true) Update @else Submit @endif
                    </button>
                </a>
                <a href="{{ route('home') }}">
                    <button type="button"
                        class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-900 font-bold py-2 px-4 rounded-r-lg">Cancel</button>
                </a>
            </div>
            </form>
        </div>
    </div>
@endsection
