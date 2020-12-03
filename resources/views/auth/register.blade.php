@extends('layouts.app')

@section('pageTitle', __('messages.page_title_user_register'))

@if (App::environment('production'))
@section('recaptcha')
<script src='https://www.google.com/recaptcha/api.js'></script>
@stop
@endif

@section('content')

<div class="max-w-sm mx-auto py-12 px-4">
    <form class="form-signin" method="POST" action="{{ route('register') }}">
        @csrf
        <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4 flex flex-col">
            <div class="mx-auto">
                <svg class="h-24 w-auto inline fill-current" viewBox="0 0 128 128"
                    xmlns="http://www.w3.org/2000/svg">
                    <path d="m80.5 70.6a1.8 1.8 0 0 0 2.4-0.4 23.5 23.5 0 1 0-37.9 0 1.8 1.8 0 1 0 2.8-2.1 20 20 0 1 1 32.2 0 1.8 1.8 0 0 0 0.4 2.4z"/>
                    <path d="m44.4 83.8a1.8 1.8 0 0 0 2-2.8 30.3 30.3 0 1 1 35.1 0 1.8 1.8 0 0 0 2 2.9 33.8 33.8 0 1 0-39.2 0z"/>
                    <path d="m96 102.1h-18.7v-22.6a13.4 13.4 0 0 0-6.8-11.6 13.4 13.4 0 1 0-13.2 0 13.4 13.4 0 0 0-6.8 11.6v22.6h-18.7a1.8 1.8 0 0 0 0 3.5h64.1a1.8 1.8 0 0 0 0-3.5zm-41.9-45.8a9.9 9.9 0 1 1 9.9 9.9 9.9 9.9 0 0 1-9.9-9.9zm0 23.2a9.9 9.9 0 0 1 19.7 0v22.6h-19.7z"/>
                </svg>
            </div>
            <div class="mb-4">
                <label class="block text-grey-darker text-sm font-bold mb-2" for="username"> First name </label>
                <input type="text" name="firstname" id="firstname" value="{{ old('firstname') }}" placeholder="Obi" required
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-grey-darker">
            </div>
            <div class="mb-4">
                <label class="block text-grey-darker text-sm font-bold mb-2" for="username"> Last name </label>
                <input type="text" name="lastname" id="lastname" value="{{ old('lastname') }}" placeholder="Wan" required
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-grey-darker">
            </div>
            <div class="mb-4">
                <label class="block text-grey-darker text-sm font-bold mb-2" for="username"> Email </label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" placeholder="obi.wan@kenobi.net" required
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-grey-darker">
            </div>

            <div class="mb-4">
                <label class="block text-grey-darker text-sm font-bold mb-2" for="password"> Password </label>
                <input type="password" name="password" id="password" required placeholder="******************"
                    class="shadow appearance-none border border-red rounded w-full py-2 px-3 text-grey-darker mb-3">
            </div>
            <div class="mb-4">
                <label class="block text-grey-darker text-sm font-bold mb-2" for="password"> Confirmation </label>
                <input type="password" name="password_confirmation" id="password_confirmation" required
                    class="shadow appearance-none border border-red rounded w-full py-2 px-3 text-grey-darker mb-3" placeholder="******************">
            </div>
            <div class="md:flex md:items-center mb-4">
                <div class="md:w-1/3"></div>
                <label for="owner" class="md:w-2/3 block text-gray-500 font-bold">
                    <input class="mr-2 leading-tight" type="checkbox" id="owner" name="owner" value="1" required="">
                    <span class="text-sm"> I accept the terms of service </span>
                </label>
            </div>
            <div class="mb-4">
                @if (App::environment('production'))
                {!! NoCaptcha::display() !!}
                @endif
            </div>
            <div class="flex items-center justify-between">
                <button class="bg-gray-900 hover:bg-gray-800 text-white font-bold py-2 px-4 rounded" type="submit"> Sign up </button>
                
                <ul class="inline-block">
                    <li>
                        <a href="{{ route('login') }}" class="inline-block align-baseline font-bold text-sm text-blue hover:text-blue-darker"> Already user </a>
                    </li>
                    <li>
                        <a href="{{ route('password.request', ['email' => old('email')]) }}" class="inline-block align-baseline font-bold text-sm text-blue hover:text-blue-darker"> 
                            Forgot Password? </a>
                    </li>
                </ul>
            </div>
        </div>
    </form>
</div>
   
@endsection