@extends('layouts.app')

@section('pageTitle', "It's a success, and welcome aboard ! ⛵" )

@section ('content')

<div class="max-w-screen-xl mx-auto py-6 md:py-12 px-4">

    <div class="bg-gray-100 border rounded-lg border-gray-500 text-gray-900 px-4 py-3" role="alert">
        <p class="font-semibold text-center">🎉 Success ! 🍾</p>
        <p class="p-4">
            Thanks a lot for your trust ! It means a lot for me 🤗.<br/>
            From now your podcast is registered to last and i will take care of it.<br/> 
        </p>
        <p class="text-center">
            <a  href="{{ route('home') }}">
                <button type="button" class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-900 font-bold py-2 px-4 rounded-lg">Back to dashboard</button>
            </a>
        </p>
    </div>
</div>

@endsection