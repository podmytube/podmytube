@if ($playlists->count())
    <h2 class="h2">
        Your @if ($playlists->count() > 1)playlists @else playlist @endif
    </h2>

    <div class="flex flex-col sm:flex-row">
        @foreach ($playlists as $playlist)
            <div
                class="mx-auto md:mx-1 max-w-md rounded-lg overflow-hidden shadow-lg bg-gray-100 mt-4 pt-4 md:pt-10 pb-4 ">
                <img class="mx-auto shadow-lg rounded-lg" src="{{ $playlist->vignetteUrl }}"
                    alt="Your best cover for {{ $playlist->title() }}">
                <div class="px-2 md:px-6 py-4 text-center">
                    <div class="font-bold text-xl text-gray-900 leading-tight mb-2">{{ $playlist->title() }}</div>
                </div>

                <div class="text-center pb-6">
                    <a href="{{ $playlist->podcastUrl() }}" target="_blank">
                        <button class="btn-podcast">
                            @php echo file_get_contents(public_path('images/podcast.svg')) @endphp
                            <span class="px-2">Your podcast feed</span>
                        </button>
                    </a>
                </div>

                <div class="px-4">
                    <div class="flex justify-center items-center">
                        <a href="{{ route('playlist.cover.edit', $playlist) }}">
                            <button
                                class="flex-1 bg-gray-200 shadow hover:bg-gray-400 text-gray-800 font-bold py-2 px-2 rounded-lg">
                                @php echo file_get_contents(public_path('images/cover.svg')) @endphp
                                Cover
                            </button>
                        </a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif
