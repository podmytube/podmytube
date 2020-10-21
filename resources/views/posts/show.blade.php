@extends('layouts.app')

@section('pageTitle', $post->title() )

@section ('content')

<header class="masthead" style="background-image: url({{$post->featured_image}})">
    <div class="overlay"></div>
    <div class="container">
        <div class="row">
            <div class="col-lg-8 col-md-10 mx-auto">
                <div class="site-heading text-white">
                    <h1>{{ $post->title() }}</h1>
                    <span class="subheading">last updated on {{ $post->updated_at->format("l jS \of F Y") }} by {{ $post->author }}</span>
                </div>
            </div>
        </div>
    </div>
</header>

<article>
    <div class="container">
        <div class="row">
            <div class="col-lg-8 col-md-10 mx-auto">
                {!! $post->content !!}
            </div>
        </div>
    </div>
</article>

<hr>

@include('partials.share', ['url' => url()->full(), 'title' => $post->title() ])

@endsection