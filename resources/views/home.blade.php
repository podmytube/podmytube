@extends('layouts.app')

@section('pageTitle', __('messages.page_title_home_index') )


@section('content')

<div class="container mx-auto">
	@include ('partials.channels')
</div>
<!--/home main container-->
@endsection