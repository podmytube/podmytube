@extends('layouts.app')

@section('pageTitle', __('messages.page_title_user_register'))

@if (env('APP_ENV') != 'dev')
    @section('recaptcha')
    <script src='https://www.google.com/recaptcha/api.js'></script>
    @stop
@endif

@section('content')
<div class="container w-50 mt-2 mb-2"><!--section container-->
	<div class="card">
		<div class="card-header">{{ __('messages.title_register_label') }}</div>
    		<div class="card-body">

                    @include ('layouts.errors')
                    
                    <form class="form-horizontal" method="POST" action="{{ route('register') }}">
                        {{ csrf_field() }}

                        <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                            <label for="name" class="col control-label">{{ __('messages.name_label') }}</label>

                            <input id="name" type="text" class="form-control" name="name" value="{{ old('name') }}" required autofocus>

                            @if ($errors->has('name'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('name') }}</strong>
                                </span>
                            @endif
                        
                        </div>

                        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                            <label for="email" class="col control-label">{{ __('messages.email_label') }}</label>
                            
                            <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required>

                            @if ($errors->has('email'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('email') }}</strong>
                                </span>
                            @endif
                            </div>

                        <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                            <label for="password" class="col control-label">{{ __('messages.password_label') }}</label>

                        
                            <input id="password" type="password" class="form-control" name="password" required>

                            @if ($errors->has('password'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('password') }}</strong>
                                </span>
                            @endif
                        </div>

                        <div class="form-group">
                            <label for="password-confirm" class="col control-label">{{ __('messages.confirm_password_label') }}</label>

                            <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required>
                        </div>

                        @if (env('APP_ENV') != 'dev')
                        {!! NoCaptcha::display() !!}
                        @endif

                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <button type="submit" class="btn btn-primary">{{ __('messages.button_register_label') }}</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div><!--/section container-->
@endsection
