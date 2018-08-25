@extends('layouts.app')

@section('pageTitle', __('messages.page_title_medias_stats_index') )

@section('charts_lib')
	<script src="https://code.highcharts.com/highcharts.src.js"></script>
@stop

@section ('content')

{{ Breadcrumbs::render('medias_stats.index', $channel) }}


{{--
<nav class="navbar navbar-expand-lg navbar-light bg-white">

    @foreach ($valid_periods as $period)

    <ul class="navbar-nav mr-auto">
      
        <li class="nav-item @if($period==$active_period)active @endif">
        
            <a class="nav-link" href="?period={{ $period }}" >
        
            {{ __('messages.channel_stats_link_'.$period.'_period') }} 
        
            </a> 
        
        </li>

    </ul>
    @endforeach

</nav>
--}}

<div class="container">

    @include ('layouts.highcharts_line')
    
</div>

@endsection
