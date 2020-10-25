@extends('layouts.app')

@section('pageTitle', $post->title() )

@section ('content')

<div class="max-w-screen-xl mx-auto text-gray-100 py-12 px-6">

    <h1 class="text-center text-white text-3xl md:text-5xl font-semibold pb-6">{{ $post->title() }}</h1>

    <div class="text-gray-500 text-sm">
        updated on {{ $post->updated_at->format("l jS \of F Y") }} by {{ $post->author }}
    </div>

    <div class="leading-normal py-6 post-content">
        {!! $post->content !!}
    </div>

    @include('partials.share', ['url' => url()->full(), 'title' => $post->title() ])
</div>

@endsection