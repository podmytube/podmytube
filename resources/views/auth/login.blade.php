@extends('layouts.authent')

@section('pageTitle',__('messages.page_title_user_login'))

@section('content')

<form class="form-signin" method="POST" action="{{ route('login') }}">
  @csrf
  <div class="text-center mb-4">
    <img class="mb-4" src="/images/logo-small.png" alt="" width="133" />
  </div>

  <div class="form-label-group">
    <input type="email" name="email" id="inputEmail" class="form-control" placeholder="{{ __('messages.email_label') }}" required autofocus />
    <label for="inputEmail">{{ __('messages.email_label') }}</label>
  </div>

  <div class="form-label-group">
    <input type="password" name="password" id="inputPassword" class="form-control" placeholder="{{ __('messages.password_label') }}" required />
    <label for="inputPassword">{{ __('messages.password_label') }}</label>
  </div>

  <div class="checkbox mb-3">
    <label> <input type="checkbox" name="remember" value="remember-me" /> {{ __('messages.remember_me_label') }} </label>
  </div>
  <button class="btn btn-lg btn-primary btn-block" type="submit">
    {{ __('messages.button_login_label') }}
  </button>

  <ul>
    <li><a href="{{ route('password.request') }}">{{ __('messages.password_forgotten_label') }} ?</a></li>
    <li>new customer ? <a href="{{ route('register') }}">{{ __('messages.button_register_label') }}</a></li>
  </ul>
</form>

@endsection