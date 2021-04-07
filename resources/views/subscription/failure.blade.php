@extends('layouts.app')

@section('pageTitle', "Something occured with your subscription." )

@section ('content')

<div class="max-w-screen-xl mx-auto py-6 md:py-12 px-4">
    <div class="bg-gray-100 border rounded-lg border-gray-500 text-gray-900 px-4 py-3" role="alert">
        <p class="font-semibold text-center">❌ Failure ! 😰</p>
        <p class="p-4">
            I'm sorry to tell you that something occured with your subscription.<br>
            You <a href="mailto:frederick@podmytube.com?subject=something%20occured%20with%20my%20subscription">may contact me</a>.
        </p>
        <p class="text-center">
            <a href="{{ route('home') }}">
                <button type="link" class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-900 font-bold py-2 px-4 rounded-lg">Back to dashboard</button>
            </a>
        </p>
    </div>
</div>

@endsection



