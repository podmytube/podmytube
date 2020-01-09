<image>
    @if ($headerImage->url())<url>{{$headerImage->url()}}</url>@endif
    @if ($headerImage->title())<title>{{$headerImage->title()}}</title>@endif
    @if ($headerImage->link())<link>{{$headerImage->link()}}</link>@endif
</image>