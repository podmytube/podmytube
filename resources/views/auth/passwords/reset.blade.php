@extends('layouts.app')

@section('pageTitle', 'Mot de passe oublié')

@section('content')
<div class="container w-50 mt-2 mb-2"><!--section container-->
	<div class="card">
		<div class="card-header">Reset Password</div>

		<div class="card-body">
			<form class="form-horizontal" method="POST" action="{{ route('password.request') }}">
				{{ csrf_field() }}

				<input type="hidden" name="token" value="{{ $token }}">

				<div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
					<label for="email" class="col-md-4 control-label">E-Mail Address</label>

					<input id="email" type="email" class="form-control" name="email" value="{{ $email or old('email') }}" required autofocus>

					@if ($errors->has('email'))
						<span class="help-block">
							<strong>{{ $errors->first('email') }}</strong>
						</span>
					@endif
				</div>

				<div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
					<label for="password" class="col-md-4 control-label">Password</label>

					<input id="password" type="password" class="form-control" name="password" required>

					@if ($errors->has('password'))
						<span class="help-block">
							<strong>{{ $errors->first('password') }}</strong>
						</span>
					@endif
				</div>

				<div class="form-group{{ $errors->has('password_confirmation') ? ' has-error' : '' }}">
					<label for="password-confirm" class="col-md-4 control-label">Confirm Password</label>
					<input id="password-confirm" type="password" class="form-control" name="password_confirmation" required>

					@if ($errors->has('password_confirmation'))
						<span class="help-block">
							<strong>{{ $errors->first('password_confirmation') }}</strong>
						</span>
					@endif
				</div>

				<div class="form-group">
					<button type="submit" class="btn btn-primary">Reset Password</button>
				</div>
			</form>
		</div>
	</div>
</div><!--/section container-->
@endsection
