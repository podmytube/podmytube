@extends('layouts.authent')

@section('pageTitle',__('messages.page_title_user_login'))

@section('content')

<form class="form-signin" method="POST" action="{{ route('login') }}">
  @csrf
  <div class="text-center mb-4">
    <img class="mb-4" src="/images/logo-small.png" alt="" width="133" />
  </div>
  
  <div class="form-label-group">
    <input type="email" name="email" id="email" class="form-control" value="{{ old('email') }}" placeholder="juste@leblanc.fr" required autofocus />
    <label for="email">{{ __('messages.email_label') }}</label>
  </div>

  <div class="form-label-group">
    <input type="password" name="password" id="password" class="form-control" placeholder="{{ __('messages.password_label') }}" required />
    <label for="password">{{ __('messages.password_label') }}</label>
  </div>

  <div class="checkbox mb-3">
    <input type="checkbox" id="remember" name="remember" value="remember-me" /> 
    <label for="remember"> {{ __('messages.remember_me_label') }} </label>
  </div>
  <button class="btn btn-lg btn-primary btn-block" type="submit">
    {{ __('messages.button_login_label') }}
  </button>

  <ul>
    <li><a href="{{ route('password.request', ['email' => old('email')]) }}">{{ __('messages.password_forgotten_label') }} ?</a></li>
    <li>new customer ? <a href="{{ route('register') }}">{{ __('messages.button_register_label') }}</a></li>
  </ul>
</form>

@endsection