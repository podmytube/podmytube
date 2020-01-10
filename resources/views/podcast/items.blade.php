@forelse ($items as $item)
<item>
    @if ($item->media_id)<guid>{{ $item->media_id}}</guid> @endif
    @if ($item->title)<title>{{ $item->title}}</title> @endif
    @if ($item->enclosure)<enclosure url="{{ $item->enclosure['url'] }}" length="{{ $item->enclosure['length'] }}" type="audio/mpeg" />@endif
    @if ($item->description)<description>{{ $item->description}}</description> @endif
    @if ($item->pubDate)<pubDate>{{ $item->pubDate}}</pubDate> @endif
    {{--
    <itunes:author>{{ podcast['title'] }}</itunes:author>
    <itunes:explicit>{{ podcast['itunes']['explicit'] }}</itunes:explicit>
    <itunes:summary>{{ item['description'] }}</itunes:summary>
    <itunes:duration>{{ item['duration'] }}</itunes:duration>
    --}}
</item>
@empty
1
@endforelse