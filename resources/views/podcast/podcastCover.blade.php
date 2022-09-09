@if ($podcastCover->url())
    <image>
        <url>{{ $podcastCover->url() }}</url>
        @if ($podcastCover->title())
            <title>{{ $podcastCover->title() }}</title>
        @endif
        @if ($podcastCover->link())
            <link>{{ $podcastCover->link() }}</link>
        @endif
    </image>
    <itunes:image href="{{ $podcastCover->url() }}" />
@endif
