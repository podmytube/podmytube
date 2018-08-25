@extends('layouts.app')

@section('pageTitle', __('messages.page_title_channel_app_stats_index') )

@section('charts_lib')
	<script src="https://code.highcharts.com/highcharts.src.js"></script>
@stop

@section ('content')

{{ Breadcrumbs::render('app_stats.index', $channel) }}


<div class="container">
    
    @include ('layouts.highcharts_pie')

</div>

@endsection
