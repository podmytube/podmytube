<image>
    @if ($podcastCover->url())<url>{{$podcastCover->url()}}</url>
    @endif
    @if ($podcastCover->title())<title>{{$podcastCover->title()}}</title>
    @endif
    @if ($podcastCover->link())<link>{{$podcastCover->link()}}</link>
    @endif
</image>