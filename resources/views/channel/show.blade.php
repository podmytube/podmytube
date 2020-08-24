@extends('layouts.app')

@section('pageTitle', __('messages.page_title_channel_show') . $channel->channel_name )

@section ('content')

<div class="container">
	<!--section container-->

	<h2>
		{{ $channel->title() }}
	</h2>

	<hr>

	<div class="row">
		<div class="col-md-8">
			<span class="font-italic align-middle">
				{{ __('messages.channel_podcast_created_label') }}
			</span> : @if ($channel->createdAt()) {{ $channel->createdAt()->format(__('localized.dateTimeFormat')) }} @endif<br />

			<span class="font-italic">
				{{ __('messages.channel_podcast_updated_label') }}
			</span> : @if ($channel->podcastUpdatedAt()) {{ $channel->podcastUpdatedAt()->format(__('localized.dateTimeFormat')) }} @else {{__('channels.never')}} @endif
		</div>
		<div class="col-md-4 text-center">
			<div class="btn-group" role="group" aria-label="Basic example">
				<a href="{{ route('channel.edit', $channel->channel_id) }}" class="btn btn-primary" role="button">
					<i class="far fa-edit"></i> {{ __('messages.button_edit_channel_label') }}
				</a>
				<a href="{{ route('channel.thumbs.edit', $channel) }}" class="btn btn-primary" role="button">
					<i class="fas fa-image"></i> {{ __('messages.button_edit_thumb_label') }}
				</a>
			</div>
		</div>
	</div>

	<div class="row mb-2 border-bottom p-0">
		<div class="col-4"><b>{{ __('messages.channel_authors_label') }}</b></div>
		<div class="col-8">{{ $channel->authors }}</div>
	</div>
	<div class="row mb-2 border-bottom p-0">
		<div class="col-4"><b>{{ __('messages.channel_email_label') }}</b></div>
		<div class="col-8">{{ $channel->email }}</div>
	</div>
	<div class="row mb-2 border-bottom p-0">
		<div class="col-4"><b>{{ __('messages.channel_link_label') }}</b></div>
		<div class="col-8">{{ $channel->link }}</div>
	</div>
	<div class="row mb-2 border-bottom p-0">
		<div class="col-4">
			<p><b>{{ __('messages.channel_category_label') }}</b></p>
			@if ($channel->category)
			{{ __("categories.".$channel->category->name) }}
			@else
			{{ __('messages.no_category_defined_label') }}
			@endif
		</div>
		<div class="col-4">
			<p><b>{{ __('messages.channel_lang_label') }}</b></p>
			{{ __("localized.".$channel->lang) }}
		</div>
	</div>

	@if ($channel->hasFilter())
	<div class="row">
		<div class="col">
			<h3>{{ __('messages.channel_filters_label') }}</h3>
			<div class="alert alert-warning" role="alert">
				{!! __('messages.filters_warning') !!}
			</div>
			<ul>
				@foreach ($channel->getFilters() as $filter)
				<li>{!! $filter !!}</li>
				@endforeach
			</ul>
		</div>
	</div>
	@endif
	@if ($channel->explicit())
	<div class="alert alert-warning" role="alert">
		{{ __('messages.channel_explicit_label') }}
	</div>
	@endif

</div>
<!--/section container-->

@endsection