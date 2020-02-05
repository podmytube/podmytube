@extends('layouts.app')

@section('pageTitle', __('messages.page_title_channel_index') )

@section ('content')

{{ Breadcrumbs::render('channel.index') }}

@include ('partials.channels')

@endsection
