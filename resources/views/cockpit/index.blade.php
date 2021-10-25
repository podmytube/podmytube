@extends('layouts.cockpit')

@section('pageTitle', 'Podmytube cockpit.')

@section('content')


    <h2>last registered channel</h2>
    <h3>{{ $lastRegisteredChannel->title() }}</h3>
    <a href="{{ $lastRegisteredChannel->podcastUrl() }}">podcast</a>
    <a href="{{ $lastRegisteredChannel->youtubeUrl() }}">channel</a>

@endsection
