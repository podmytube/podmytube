@extends('layouts.app')

@section('pageTitle', 'Tableau de bord')

@section('content')
<div class="container"> <!--home main container-->
	@if (session('status'))
	<div class="alert alert-success" role="alert">
			{{ session('status') }}
	</div>
	@endif

	<h1>Dashboard</h1>

	<hr>

	@if ( count(Auth::user()->channels) > 0 )
	<div class="container"> <!--channel container-->

		<h2>Chaines</h2>

		<ul>

		@foreach (Auth::user()->channels as $channel)

			<li><a href="/channel/<?= $channel->channel_id?>"><?= $channel->channel_name ?></a></li>

		@endforeach

		</ul>

	</div><!--/channel container-->

	@endif
</div><!--/home main container-->
@endsection
