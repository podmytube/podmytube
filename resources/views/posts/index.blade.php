@extends('layouts.blog')

@section('pageTitle', __('medias.page_title_index') )

@section ('content')

@if ($posts->count())
@foreach($posts as $post)
<div>
    <div>
        <img src="{{ $post->featured_image }}">
    </div>
    <a href="{{ route('post.show', $post) }}">{{ $post->title() }}</a>
    {!! $post->excerpt !!}
</div>
@endforeach
@else
<div class="alert alert-dark" role="alert">
    {!! __('medias.index_channel_has_no_known_episode') !!}
</div>
@endif
@endsection