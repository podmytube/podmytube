@extends('layouts.app')

@section('pageTitle', 'Podmytube list of users')

@section('content')
    <div class="max-w-screen-xl mx-auto py-12 px-4">

        <h2 class="text-3xl md:text-5xl text-white font-semibold">Users</h2>

        <livewire:users />

    </div>
@endsection
