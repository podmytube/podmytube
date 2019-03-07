@extends('layouts.app', ['var' => 1])

@section('pageTitle', __('messages.page_title_user_show') )

@section ('content')

{{ Breadcrumbs::render('user.show', $user) }}

<div class="container"><!--section container-->

    <h2> {{ __('messages.page_title_user_show') }} </h2>
    
	<hr> 
    
	<div class="container">
		<div class="row">
			<div class="col">
                {{ __('messages.name_label') }}
			</div>
			<div class="col">
                {{ $user->name }}
			</div>
		</div>
        <div class="row">
			<div class="col">
                {{ __('messages.email_label') }}
			</div>
			<div class="col">
                {{ $user->email }}
			</div>
		</div>
        <div class="row">
			<div class="col">
                {{ __('messages.language_label') }}
			</div>
			<div class="col">
                {{ $user->language }}
			</div>
		</div>
    </div>

    <div class="mx-auto" style="width:200px">
        <a href="{{ route('user.edit') }}"><button type="button" class="btn btn-primary">{{ __('messages.button_modify_label') }}</button></a>
    </div>


</div><!--/section container-->

@endsection
