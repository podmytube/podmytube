@extends('layouts.app')

@section('pageTitle', 'Analytics ⚡')

@section('content')

    <div class="max-w-screen-xl mx-auto py-6 md:py-12 px-4">
        <h2 class="text-3xl md:text-5xl text-white font-semibold">Analytics ⚡</h2>

        <livewire:charts :channel="$channel" />

        <canvas id="analytics"></canvas>
    </div>
@endsection
