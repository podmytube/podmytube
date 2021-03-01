@extends('layouts.app')

@section('pageTitle',__('messages.page_title_lost_password'))

@section('content')

<div class="max-w-xs mx-auto py-12">
	<div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4 flex flex-col">
		<form class="form-signin" method="POST" action="{{ route('password.request') }}">
			@csrf
			<div class="text-center">
				<svg class="h-24 w-auto inline fill-current" viewBox="0 0 128 128"
					xmlns="http://www.w3.org/2000/svg">
					<path d="m80.5 70.6a1.8 1.8 0 0 0 2.4-0.4 23.5 23.5 0 1 0-37.9 0 1.8 1.8 0 1 0 2.8-2.1 20 20 0 1 1 32.2 0 1.8 1.8 0 0 0 0.4 2.4z"/>
					<path d="m44.4 83.8a1.8 1.8 0 0 0 2-2.8 30.3 30.3 0 1 1 35.1 0 1.8 1.8 0 0 0 2 2.9 33.8 33.8 0 1 0-39.2 0z"/>
					<path d="m96 102.1h-18.7v-22.6a13.4 13.4 0 0 0-6.8-11.6 13.4 13.4 0 1 0-13.2 0 13.4 13.4 0 0 0-6.8 11.6v22.6h-18.7a1.8 1.8 0 0 0 0 3.5h64.1a1.8 1.8 0 0 0 0-3.5zm-41.9-45.8a9.9 9.9 0 1 1 9.9 9.9 9.9 9.9 0 0 1-9.9-9.9zm0 23.2a9.9 9.9 0 0 1 19.7 0v22.6h-19.7z"/>
				</svg>
			</div>

			<input type="hidden" name="token" value="{{ $token }}">

			<div class="mb-4 mt-2">
				<label class="block text-grey-darker text-sm font-bold mb-2" for="email"> Email address </label>
				<input type="email" name="email" id="email" class="shadow appearance-none border rounded w-full py-2 px-3 text-grey-darker" value="{{ $email }}" required autofocus>
			</div>

			<div class="mb-4 mt-2">
				<label class="block text-grey-darker text-sm font-bold mb-2" for="password">{{ __('messages.password_label') }}</label>
				<input type="password" name="password" id="password" class="shadow appearance-none border rounded w-full py-2 px-3 text-grey-darker" placeholder="{{ __('messages.password_label') }}" required />
			</div>

			<div class="mb-4 mt-2">
				<label class="block text-grey-darker text-sm font-bold mb-2" for="password_confirmation">{{ __('messages.confirm_password_label') }}</label>
				<input type="password" name="password_confirmation" id="password_confirmation" class="shadow appearance-none border rounded w-full py-2 px-3 text-grey-darker" placeholder="{{ __('messages.confirm_password_label') }}" required />
			</div>

			<div class="text-center">
				<button class="bg-gray-900 hover:bg-gray-800 text-white font-bold py-2 px-4 rounded text-center" type="submit">
					{{ __('messages.button_reset_password') }}
				</button>
			</div>
		</form>
	</div>
</div>

@endsection