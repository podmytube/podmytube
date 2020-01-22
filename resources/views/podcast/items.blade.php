@forelse ($medias as $media)
<item>
    <guid>{{ $media->media_id}}</guid>
    <title>{{ $media->title}}</title>
    <enclosure url="{{ $media->enclosureUrl() }}" length="{{ $media->length }}" type="audio/mpeg" />
    <pubDate>{{ $media->pubDate()}}</pubDate>
    @if ($media->description)<description>{{ $media->description}}</description>
    @endif
    <itunes:duration>{{ $media->duration() }}</itunes:duration>
    <itunes:explicit>{{ $media->channel->explicit() }}</itunes:explicit>
</item>
@empty
{{-- no items to publish --}}
@endforelse