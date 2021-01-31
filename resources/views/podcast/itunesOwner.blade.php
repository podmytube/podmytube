@if ($itunesOwner->name)<itunes:author>{{$itunesOwner->name}}</itunes:author> @endif
<itunes:owner>
    @if ($itunesOwner->name)<itunes:name>{{$itunesOwner->name}}</itunes:name> @endif
    @if ($itunesOwner->email)<itunes:email>{{$itunesOwner->email}}</itunes:email> @endif
</itunes:owner>