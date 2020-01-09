@if ($podcastHeader->link())<link>{{ $podcastHeader->link() }}</link>
@endif
@if ($podcastHeader->title())<title>{{ $podcastHeader->title() }}</title>
@endif
@if ($podcastHeader->language())<language>{{ $podcastHeader->language() }}</language>
@endif
@if ($podcastHeader->copyright())<copyright>{{ $podcastHeader->copyright() }}</copyright>
@endif
@if ($podcastHeader->description())<description><![CDATA[{{ $podcastHeader->description() }}]]></description>
@endif

{{-- header image --}}
@if ($podcastHeader->podcastCover())
@include ('podcast.podcastCover', ["podcastCover" => $podcastHeader->podcastCover()])
@endif

{{-- itunes part --}}
@if ($podcastHeader->itunesHeader())
@include ('podcast.itunesHeader', ["itunesHeader" => $podcastHeader->itunesHeader()])
@endif