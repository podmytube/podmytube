@extends('layouts.app') 

@section('pageTitle', __('messages.page_title_home_index') )

@section('breadcrumbs') 

	{{ Breadcrumbs::render('home') }}

@endsection

@section('content') 

@include ('layouts.errors')

<div class="container">

	<!--home main container-->
	
	<div class="row">

		<div class="col">

			@include ('layouts.channels')

		</div>

	</div>



</div>
<!--/home main container-->
@endsection