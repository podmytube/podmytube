@extends('layouts.app')

@section('pageTitle', __('messages.page_title_channel_index') )

@section ('content')

@include ('partials.channels')

@endsection
