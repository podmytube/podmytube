@extends('layouts.authent')

@section('pageTitle',__('messages.page_title_lost_password'))

@section('content')
<form class="form-signin" method="POST" action="{{ route('password.request') }}">
	@csrf
	<div class="text-center mb-4">
		<img class="mb-4" src="/images/logo-small.png" alt="" width="133" />
	</div>

	<input type="hidden" name="token" value="{{ $token }}">

	<div class="form-label-group">
		<input type="email" name="email" id="inputEmail" class="form-control" placeholder="{{ __('messages.email_label') }}" value="{{ $email }}" required autofocus>
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
		{{ __('messages.button_reset_password') }}
	</button>
	
</form>

@endsection