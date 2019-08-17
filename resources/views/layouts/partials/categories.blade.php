<option value="0">
    {{ __('categories.select_your_category') }}
</option>
@if ( count ($categories) > 0 )
    @foreach ($categories as $category)
        <option value="{{ $category->id }}"{{ htmlspecialchars($channelSelectedCategory) == $category ? ' selected' : '' }}>
            {{ $category->name }}
        </option>
        @if ($category->children)
            @foreach ($category->children as $child)
            <option value="{{$child->id}}"{{ htmlspecialchars($channelSelectedCategory) == $category ? ' selected' : '' }} >
                -- {{__("categories.".$child->name)}}
            </option>
            @endforeach
        @endif
    @endforeach
@endif