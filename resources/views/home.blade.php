@extends('layouts.app')

@section('pageTitle', __('messages.page_title_home_index') )

@section('breadcrumbs')

{{ Breadcrumbs::render('home') }}

@endsection

@section('content')

<div class="container">
	@include ('partials.channels')
</div>
<!--/home main container-->
@endsection