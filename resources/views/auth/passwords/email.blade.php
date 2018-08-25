@extends('layouts.app')

@section('pageTitle', 'Mot de passe oublié')

@section('content')
<div class="container w-50 mt-2 mb-2"><!--section container-->
	<div class="card">
		<div class="card-header">Reset Password</div>

		<div class="card-body">
			@if (session('status'))
				<div class="alert alert-success">
					{{ session('status') }}
				</div>
			@endif

			<form class="form-horizontal" method="POST" action="{{ route('password.email') }}">
				{{ csrf_field() }}

				<div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
					<label for="email" class="col-md-4 control-label">E-Mail Address</label>

					<input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required>

					@if ($errors->has('email'))
						<span class="help-block">
							<strong>{{ $errors->first('email') }}</strong>
						</span>
					@endif
				</div>

				<div class="form-group">
					<button type="submit" class="btn btn-primary">
						Send Password Reset Link
					</button>
				</div>
			</form>
		</div>
	</div>
</div>
@endsection
