@extends('layouts.app')

@section('pageTitle', __('messages.page_title_user_login'))

@section('content')
<div class="container w-50 mt-2 mb-2"><!--section container-->
	<div class="card">
		<div class="card-header">{{ __('messages.title_login_label') }}</div>
			<div class="card-body">

				@include ('layouts.errors')

				<form class="form-horizontal" method="POST" action="{{ route('login') }}">
					{{ csrf_field() }}

					<div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
						<label for="email" class="col-md-4 control-label">{{ __('messages.email_label') }}</label>

						<input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required autofocus>

						@if ($errors->has('email'))
							<span class="help-block">
								<strong>{{ $errors->first('email') }}</strong>
							</span>
						@endif
					</div>

					<div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
						<label for="password" class="col-md-4 control-label">{{ __('messages.password_label') }}</label>

						<input id="password" type="password" class="form-control" name="password" required>

						@if ($errors->has('password'))
							<span class="help-block">
								<strong>{{ $errors->first('password') }}</strong>
							</span>
						@endif
					</div>

					<div class="form-group">
						<div class="checkbox">
							<label>
								<input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}> {{ __('messages.remember_me_label') }}
							</label>
						</div>
					</div>

					<div class="form-group">
						<button type="submit" class="btn btn-primary">{{ __('messages.button_login_label') }}</button>

						<a class="btn btn-link" href="{{ route('password.request') }}">{{ __('messages.password_forgotten_label') }} ?</a>
					</div>
				</form>
			</div>
		</div>
	</div>
</div><!--/section container-->
@endsection
