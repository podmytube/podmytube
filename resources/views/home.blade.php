@extends('layouts.app')

@section('pageTitle', __('messages.page_title_home_index') )


@section('content')

<div class="max-w-screen-xl mx-auto text-gray-100 py-12 px-4">
	@include ('partials.channels')
</div>
<!--/home main container-->
@endsection