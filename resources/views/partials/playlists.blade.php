@if ($playlists->count())
    <h2 class="text-3xl md:text-5xl text-white font-semibold my-3">
        Your playlists
    </h2>

    <ul>
        @foreach ($playlists as $playlist)
            <li>
                {{ $playlist->title }}
                <a href="{{ route('playlist.cover.edit', $playlist) }}" class="underline">Update cover</a>
                @if ($playlist->active) ✅ @endif
            </li>
        @endforeach
    </ul>
@endif
