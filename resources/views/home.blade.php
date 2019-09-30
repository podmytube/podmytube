@extends('layouts.app') 

@section('pageTitle', __('messages.page_title_home_index') )

@section('breadcrumbs') 

	{{ Breadcrumbs::render('home') }}

@endsection

@section('content') 

@include ('layouts.errors')

<div class="container">
	@include ('layouts.channels')
</div>
<!--/home main container-->
@endsection