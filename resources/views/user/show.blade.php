@extends('layouts.app', ['var' => 1])

@section('pageTitle', __('messages.page_title_user_show') )

@section ('content')

<div class="container">
	<!--section container-->

	<h2> {{ __('messages.page_title_user_show') }} </h2>

	<hr>

	<div class="container">
		<div class="row">
			<div class="col"> Your nice firstname </div>
			<div class="col"> {{ $user->firstname }} </div>
		</div>
		<div class="row">
			<div class="col"> What a wonderful lastname </div>
			<div class="col"> {{ $user->lastname }} </div>
		</div>
		<div class="row">
			<div class="col"> Unforgettable email address </div>
			<div class="col"> {{ $user->email }} </div>
		</div>
		<div class="row">
			<div class="col">Newsletter</div>
			<div class="col">
				@if ($user->newsletter)
				<i class="fas fa-check-square text-success"></i>
				@else
				<i class="fas fa-times-circle text-danger"></i>
				@endif
			</div>
		</div>
	</div>

	<div class="mx-auto" style="width:200px">
		<a href="{{ route('user.edit', $user ) }}"><button type="button" class="btn btn-success">{{ __('messages.button_modify_label') }}</button></a>
	</div>


</div>
<!--/section container-->

@endsection