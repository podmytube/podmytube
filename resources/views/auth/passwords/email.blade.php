@extends('layouts.authent')

@section('pageTitle',__('messages.page_title_lost_password'))

@section('content')

<form class="form-signin" method="POST" action="{{ route('password.email') }}">
	@csrf
	<div class="text-center mb-4">
		<img class="mb-4" src="/images/logo-small.png" alt="" width="133" />
	</div>

	@if (session('status'))
	<div class="alert alert-success">
		{{ session('status') }}
	</div>
	@endif

	<div class="form-label-group">
		<input type="email" name="email" id="inputEmail" class="form-control" placeholder="{{ __('messages.email_label') }}" value="{{ $email }}" required autofocus />
		<label for="inputEmail">{{ __('messages.email_label') }}</label>
	</div>

	<button class="btn btn-lg btn-primary btn-block" type="submit">
		{{ __('messages.button_lost_password') }}
	</button>

	<ul>
		<li>Happy customer ? <a href="{{ route('login') }}">{{ __('messages.button_login_label') }} ?</a></li>
		<li>Want to become one ? <a href="{{ route('register') }}">{{ __('messages.button_register_label') }}</a></li>
	</ul>
</form>

@endsection