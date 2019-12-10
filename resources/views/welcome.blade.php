@extends('layouts.app') 

@section('pageTitle', __('messages.page_title_home_index') )

@section('breadcrumbs') 

	{{ Breadcrumbs::render('welcome') }}

@endsection

@section('content') 

@include ('layouts.errors')

<div class="container">
	Welcome
</div>
<!--/home main container-->
@endsection