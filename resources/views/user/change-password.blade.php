@extends('layouts.app')

@section ('pageTitle', 'Modifier votre mot de passe ')

@section('content')
<div class="container w-50 mt-2 mb-2"><!--section container-->
    <div class="card">
		<div class="card-header">Change Password</div>
            <div class="card-body">
                @if (Session::has('success'))
                    <div class="alert alert-success">{!! Session::get('success') !!}</div>
                @endif
                @if (Session::has('failure'))
                    <div class="alert alert-danger">{!! Session::get('failure') !!}</div>
                @endif
                <form action="{{ route('password.update') }}" method="post" role="form" class="form-horizontal">
                    {{csrf_field()}}

                        <div class="form-group{{ $errors->has('old') ? ' has-error' : '' }}">
                            <label for="password" class="col-md-4 control-label">Old Password</label>

                            <input id="password" type="password" class="form-control" name="old">

                            @if ($errors->has('old'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('old') }}</strong>
                                </span>
                            @endif
                        
                        </div>


                        <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                            <label for="password" class="col-md-4 control-label">Password</label>

                            <input id="password" type="password" class="form-control" name="password">

                            @if ($errors->has('password'))
                                <span class="help-block">
                                <strong>{{ $errors->first('password') }}</strong>
                            </span>
                            @endif
                        
                        </div>

                        <div class="form-group{{ $errors->has('password_confirmation') ? ' has-error' : '' }}">
                            <label for="password-confirm" class="col-md-4 control-label">Confirm Password</label>

                            <input id="password-confirm" type="password" class="form-control" name="password_confirmation">

                            @if ($errors->has('password_confirmation'))
                                <span class="help-block">
                                <strong>{{ $errors->first('password_confirmation') }}</strong>
                            </span>
                            @endif
                        
                        </div>

                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <button type="submit" class="btn btn-primary form-control">{{ __('messages.button_modify_label') }}</button>
                            </div>
                        </div>
                </form>
                </div>

            </div>
        </div>
    </div>
</div><!--/section container-->
@endsection

@section('scripts')

@endsection