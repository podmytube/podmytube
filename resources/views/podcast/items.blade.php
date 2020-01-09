@forelse ($items as $item)
<item>
    @if ($item->title)<title>{{ item->title}}</title> @endif
    <link>{{ item['enclosure']['url'] }}</link>
    <guid>{{ item['media_id'] }}</guid>
    <description>{{ item['description'] }}</description>
    <enclosure url="{{ item['enclosure']['url'] }}" length="{{ item['enclosure']['length'] }}" type="audio/mpeg" />
    <pubDate>{{ item['pubDate'] }}</pubDate>

    <itunes:author>{{ podcast['title'] }}</itunes:author>
    <itunes:explicit>{{ podcast['itunes']['explicit'] }}</itunes:explicit>
    <itunes:summary>{{ item['description'] }}</itunes:summary>
    <itunes:duration>{{ item['duration'] }}</itunes:duration>
</item>
@empty
{{-- <!--nothing to display--> --}}
@endforelse