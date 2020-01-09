@if ($itunesHeader->author())<itunes:author>{{$itunesHeader->author()}}</itunes:author>@endif
@if ($itunesHeader->title())<itunes:title>{{$itunesHeader->title()}}</itunes:title>@endif
@if ($itunesHeader->type())<itunes:type>{{$itunesHeader->type()}}</itunes:type>@endif
@if ($itunesHeader->itunesOwner()) 
  @include ('podcast.itunesOwner', ["itunesOwner" => $itunesHeader->itunesOwner()])
@endif
@if ($itunesHeader->itunesCategory()) 
  @include ('podcast.itunesCategory', ["itunesCategory" => $itunesHeader->itunesCategory()])
@endif
@if ($itunesHeader->imageUrl())<itunes:image href="{{$itunesHeader->imageUrl()}}" />@endif
@if ($itunesHeader->explicit())<itunes:explicit>{{$itunesHeader->explicit()}}</itunes:explicit>@endif