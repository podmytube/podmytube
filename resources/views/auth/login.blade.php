@extends('layouts.app')

@section('pageTitle',__('messages.page_title_user_login'))

@section('content')

<div class="max-w-xs mx-auto py-12">
  <form class="form-signin" method="POST" action="{{ route('login') }}">
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
        <label class="block text-grey-darker text-sm font-bold mb-2" for="username"> Email address </label>
        <input type="email" name="email" id="email" class="shadow appearance-none border rounded w-full py-2 px-3 text-grey-darker" placeholder="yoda@usetheforce.com">
      </div>
      <div class="mb-6">
        <label class="block text-grey-darker text-sm font-bold mb-2" for="password"> Password </label>
        <input type="password" name="password" id="password" class="shadow appearance-none border border-red rounded w-full py-2 px-3 text-grey-darker mb-3" placeholder="******************">
      </div>
      <div class="flex items-center justify-between">
        <button class="bg-gray-900 hover:bg-gray-800 text-white font-bold py-2 px-4 rounded" type="submit">
          Sign In
        </button>
        
        <ul class="inline-block">
          <li>
            <a href="{{ route('register') }}" class="inline-block align-baseline font-bold text-sm text-blue hover:text-blue-darker"> Register </a>
          </li>
          <li>
            <a href="{{ route('password.request', ['email' => old('email')]) }}" class="inline-block align-baseline font-bold text-sm text-blue hover:text-blue-darker"> Forgot Password? </a>
          </li>
        </ul>
      </div>
    </div>
  </form>
</div>

@endsection