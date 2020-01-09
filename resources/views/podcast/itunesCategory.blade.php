@if ($itunesCategory->parentName())
<itunes:category text="@lang( 'categories.'.$itunesCategory->parentName() )">
    <itunes:category text="@lang( 'categories.'.$itunesCategory->name() )" />
</itunes:category>
@else
<itunes:category text="@lang( 'categories.'.$itunesCategory->name() )" />@endif