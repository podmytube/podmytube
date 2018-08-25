@extends('layouts.app') 

@section('pageTitle', 'Tableau de bord') 
@section('pageTitle', __('messages.page_title_home_index') )

@section('content') 

{{ Breadcrumbs::render('home') }}

<div class="container">
	<!--home main container-->
	@if (session('status'))
	<div class="alert alert-success" role="alert">
		{{ session('status') }}
	</div>
	@endif 
	
	@if (session('success'))
		<h3>{{ Session::get('success') }}</h3>
	@endif

	<h1>{{ __('messages.title_application_label') }}</h1>

	<hr>

	<div class="row">

		<div class="col">

			@include ('layouts.channels')

		</div>

	</div>



</div>
<!--/home main container-->
@endsection