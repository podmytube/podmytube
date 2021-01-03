<item>
    <guid>{{ $item->guid }}</guid>
    <title>{{ $item->title }}</title>
    <enclosure url="{{ $item->enclosureUrl }}" length="{{ $item->mediaLength }}" type="audio/mpeg" />
    <pubDate>{{ $item->pubDate}}</pubDate>
    @if ($item->description)<description>{{ $item->description}}</description>
    @endif
    <itunes:duration>{{ $item->duration }}</itunes:duration>
    <itunes:explicit>{{ $item->explicit }}</itunes:explicit>
</item>