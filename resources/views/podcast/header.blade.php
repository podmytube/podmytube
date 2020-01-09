@if ($podcastHeader->title())<title>{{ $podcastHeader->title() }}</title>@endif
@if ($podcastHeader->link())<link>{{ $podcastHeader->link() }}</link>@endif
@if ($podcastHeader->description())<description><![CDATA[{{ $podcastHeader->description() }}]]></description>@endif
@if ($podcastHeader->language())<language>{{ $podcastHeader->language() }}</language>@endif
@if ($podcastHeader->copyright())<copyright>{{ $podcastHeader->copyright() }}</copyright>@endif
<docs>http://blogs.law.harvard.edu/tech/rss</docs>
<lastBuildDate>{{ podcast['lastBuildDate'] }}</lastBuildDate>
<generator>{{ podcast['generator'] }}</generator>
<pubDate>{{ podcast['pubDate'] }}</pubDate>
<image>
    <url>{{podcast['imageUrl']}}</url>
    <title>{{podcast['title']}}</title>
    <link>{{podcast['link']}}</link>
</image>
<webMaster>{{ podcast['webmaster'] }}</webMaster>

@foreach ($podcastHeader->categories as $category)
    @if ($category->parent)
    <itunes:category text="{{$category->parentName}}">
        <itunes:category text="{{$category->name}}" />
    </itunes:category>
    @else
    <itunes:category text="{{$category->name}}" />
    @endif
@endforeach

{{-- itunes part --}}
@if ($itunesHeader->author())<itunes:author>{{$itunesHeader->author()}}</itunes:author>@endif
@if ($itunesHeader->type())<itunes:type>{{$itunesHeader->type()}}</itunes:type>@endif
@if ($itunesOwner())
<itunes:owner>
    @if ($itunesHeader->name())<itunes:name>{{$itunesHeader->name()}}</itunes:name>@endif
    @if ($itunesHeader->email())<itunes:email>{{$itunesHeader->email()}}</itunes:email>@endif
</itunes:owner>
@endif
@if ($itunesHeader->imageUrl())<itunes:image>{{$itunesHeader->imageUrl()}}</itunes:image>@endif

<itunes:category text="Sports">
    <itunes:category text="Wilderness" />
</itunes:category>
@if ($itunesHeader->explicit())<itunes:explicit>{{$itunesHeader->explicit()}}</itunes:explicit>@endif