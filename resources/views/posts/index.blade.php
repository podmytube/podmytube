@extends('layouts.blog')

@section('pageTitle', __('medias.page_title_index') )

@section ('content')

<!-- Page Header -->
<header class="masthead">
    <div class="overlay"></div>
    <div class="container">
        <div class="row">
            <div class="col-lg-8 col-md-10 mx-auto">
                <div class="site-heading">
                    <h1>News from Podmytube</h1>
                    <span class="subheading">Let's talk together.</span>
                </div>
            </div>
        </div>
    </div>
</header>

@if ($posts->count())
@foreach($posts as $post)
<div class="container">
    <div class="row">
        <div class="col-lg-8 col-md-10 mx-auto">
            <div class="post-preview">
                <a href="{{ route('post.show', $post) }}">
                    <h2 class="post-title">{!! $post->title() !!}</h2>
                    <p class="post-subtitle">{!! $post->excerpt !!}</p>
                </a>
                <p class="post-meta">Posted by {{ $post->author }} on {{ $post->lastUpdate() }}</p>
            </div>
            <hr>
        </div>
    </div>
</div>
@endforeach
@endif

@endsection