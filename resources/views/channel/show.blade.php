@extends('layouts.app')

@section('pageTitle', __('messages.page_title_channel_show') . $channel->channel_name )

@section ('content')

{{ Breadcrumbs::render('channel.show', $channel) }}

<div class="container">
	<!--section container-->

	<h2>
		{{ $channel->title() }}
	</h2>

	<hr>

	<div class="container">
		<div class="row">
			<div class="col-6">
				<p>
					<a href="{{ $channel->youtube_url }}" target="_blank"><img src="/images/youtube-32x32.png" /></a>
					<a href="{{ $channel->feed_url }}" target="_blank"><img src="/images/itunes-32x32.png" /></a>
					<br />
					<span class="font-italic">
						{{ __('messages.channel_podcast_created_label') }}
					</span> : @if ($channel->createdAt()) {{ $channel->createdAt()->format(__('localized.dateFormat')) }} @endif<br />

					<span class="font-italic">
						{{ __('messages.channel_podcast_updated_label') }}
					</span> : @if ($channel->podcastUpdatedAt()) {{ $channel->podcastUpdatedAt()->format(__('localized.dateFormat')) }} @else {{__('channels.never')}} @endif
				</p>
			</div>
			<div class="col-3 align-items-center text-center">

				<a href="{{ route('channel.thumbs.edit', $channel) }}"><button type="button" class="btn btn-primary button-margin">{{ __('messages.button_update_thumbs_label') }}</button></a>

			</div>

			<div class="col-3 align-items-center text-center">

				<a href="{{ route('channel.edit', $channel->channel_id) }}"><button type="button" class="btn btn-primary button-margin">{{ __('messages.button_update_channel_label') }}</button></a>


			</div>
		</div>
	</div>

	<table class="table table-striped">
		<tbody>
			<tr>
				<th scope="row">

					{{ __('messages.channel_podcast_name_label') }}

					<a href="#" title="{{ __('messages.channel_podcast_name_help') }}"><img src="/images/glyphicons-195-question-sign.png" class="float-right"></a>

				</th>
				<td>
					{{ $channel->title() }}
				</td>
			</tr>
			<tr>
				<th scope="row">

					{{ __('messages.channel_authors_label') }}

					<a href="#" title="{{ __('messages.channel_authors_help') }}"><img src="/images/glyphicons-195-question-sign.png" class="float-right"></a>

				</th>
				<td>{{ $channel->authors }}</td>
			</tr>
			<tr>
				<th scope="row">

					{{ __('messages.channel_email_label') }}

					<a href="#" title="{{ __('messages.channel_email_help') }}"><img src="/images/glyphicons-195-question-sign.png" class="float-right"></a>

				</th>
				<td>{{ $channel->email }}</td>
			</tr>
			<tr>
				<th scope="row">

					{{ __('messages.channel_link_label') }}

					<a href="#" title="{{ __('messages.channel_link_help') }}"><img src="/images/glyphicons-195-question-sign.png" class="float-right"></a>

				</th>
				<td>{{ $channel->link }}</td>
			</tr>
			<tr>
				<th scope="row">

					{{ __('messages.channel_category_label') }}

					<a href="#" title="{{ __('messages.channel_category_help') }}"><img src="/images/glyphicons-195-question-sign.png" class="float-right"></a>

				</th>
				<td>
					@if ($channel->category)
					{{ __("categories.".$channel->category->name) }}
					@else
					{{ __('messages.no_category_defined_label') }}
					@endif

				</td>

			</tr>
			<tr>
				<th scope="row">

					{{ __('messages.channel_lang_label') }}

					<a href="#" title="{{ __('messages.channel_lang_help') }}"><img src="/images/glyphicons-195-question-sign.png" class="float-right"></a>

				</th>
				<td>{{ $channel->lang }}</td>
			</tr>
			<tr>
				<th scope="row">

					{{ __('messages.channel_explicit_label') }}

					<a href="#" title="{{ __('messages.channel_explicit_help') }}"><img src="/images/glyphicons-195-question-sign.png" class="float-right"></a>

				</th>
				<td><?= $channel->explicit == 1 ? __('messages.yes') : __('messages.no') ?></td>
			</tr>
			<tr>
				<th scope="row">

					{{ __('messages.channel_filter_by_tag_label') }}

					<a href="#" title="{{ __('messages.channel_filter_by_tag_help') }}"><img src="/images/glyphicons-195-question-sign.png" class="float-right"></a>

				</th>
				<td>

					@if ($channel->accept_video_by_tag)
					{{ $channel->accept_video_by_tag }}
					@else
					{{ __('messages.no_filter_defined_label') }}
					@endif

				</td>
			</tr>
			<tr>
				<th scope="row">

					{{ __('messages.channel_filter_by_keyword_label') }}

					<a href="#" title="{{ __('messages.channel_filter_by_keyword_help') }}"><img src="/images/glyphicons-195-question-sign.png" class="float-right"></a>

				</th>
				<td>
					@if ($channel->reject_video_by_keyword)
					{{ $channel->reject_video_by_keyword }}
					@else
					{{ __('messages.no_filter_defined_label') }}
					@endif

				</td>
			</tr>
	</table>
</div>
<!--/section container-->

@endsection