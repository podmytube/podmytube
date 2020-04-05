<option value="">
    {{ __('categories.select_your_category') }}
</option>
@if ( count ($categories) > 0 )
    @foreach ($categories as $category)
        <option value="{{ $category->id }}"{{  $channelSelectedCategory === $category->id ? " selected" : "" }}>
        {{ __('categories.'.$category->name) }}
        </option>
        @if ($category->children)
            @foreach ($category->children as $child)
            <option value="{{$child->id}}"{{  $channelSelectedCategory === $child->id ? " selected" : "" }}>
                -- {{ __('categories.'.$child->name) }}
            </option>
            @endforeach
        @endif
    @endforeach
@endif