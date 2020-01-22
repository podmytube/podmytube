@if ($itunesCategory->parentName())
<itunes:category text="{!! $itunesCategory->parentName() !!}">
    <itunes:category text="{!! $itunesCategory->name() !!}" />
</itunes:category>
@else
<itunes:category text="{!! $itunesCategory->name() !!}" />
@endif