@extends('layouts.app')

@section('pageTitle', html_entity_decode($post->title) )

@section ('content')

<div class="text-center">
    <h1>{{ html_entity_decode($post->title) }}</h1>
    {!! $post->content !!}
</div>

@endsection