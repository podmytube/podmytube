@extends('layouts.app')

@section('pageTitle', $post->title() )

@section ('content')

<div class="text-center">
    <h1>{{ $post->title() }}</h1>
    <p> last updated on {{ $post->updated_at->format("l jS \of F Y") }} </p>
    <p> by {{ $post->author }} </p>
    {!! $post->content !!}

    @include('partials.share', ['url' => url()->full(), 'title' => $post->title])
</div>

@endsection