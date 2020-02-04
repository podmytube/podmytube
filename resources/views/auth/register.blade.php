@extends('layouts.authent')

@section('pageTitle', __('messages.page_title_user_register'))

@if (env('APP_ENV') != 'dev')
@section('recaptcha')
<script src='https://www.google.com/recaptcha/api.js'></script>
@stop
@endif

@section('content')

<form class="form-signin" method="POST" action="{{ route('register') }}">
    @csrf
    <div class="text-center mb-4">
        <img class="mb-4" src="/images/logo-small.png" alt="" width="133" />
    </div>

    @include ('layouts.errors')
    <div class="form-label-group">
        <input type="text" name="name" id="inputName" class="form-control" placeholder="{{ __('messages.name_label') }}" value="{{ old('name') }}" required autofocus />
        <label for="inputName">{{ __('messages.name_label') }}</label>
    </div>

    <div class="form-label-group">
        <input type="email" name="email" id="inputEmail" class="form-control" placeholder="{{ __('messages.email_label') }}" value="{{ old('password') }}" required />
        <label for="inputEmail">{{ __('messages.email_label') }}</label>
    </div>

    <div class="form-label-group">
        <input type="password" name="password" id="inputPassword" class="form-control" placeholder="{{ __('messages.password_label') }}" required />
        <label for="inputPassword">{{ __('messages.password_label') }}</label>
    </div>

    <div class="form-label-group">
        <input type="password" name="password_confirmation" id="inputPasswordConfirmation" class="form-control" placeholder="{{ __('messages.confirm_password_label') }}" required />
        <label for="inputPasswordConfirmation">{{ __('messages.confirm_password_label') }}</label>
    </div>

    <button class="btn btn-lg btn-primary btn-block" type="submit">
        {{ __('messages.button_register_label') }}
    </button>

    <div class="mt-2">
        already customer ? <a href="{{ route('login') }}"> {{ __('messages.button_login_label') }} </a>
    </div>
    <p class="mt-5 mb-3 text-muted text-center">&copy; 2017-2020</p>
</form>
@endsection