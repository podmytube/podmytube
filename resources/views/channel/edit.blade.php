@extends ('layouts.app')

@section('pageTitle', __('messages.page_title_channel_edit') . $channel->channel_name )

@section ('content')

{{ Breadcrumbs::render('channel.edit', $channel) }}

<div class="container">
	<!--section container-->

	<h2>
		{{ $channel->channel_name }}
	</h2>

	<hr>

	<form method="POST" action="{{ route('channel.update', $channel) }}">

		{{ method_field('PATCH') }}

		{{ csrf_field() }}

		<div class="row mb-2">
			<label for="podcastName" class="col-md-2">
				{{ __('messages.channel_podcast_name_label') }}
				<i class="fas fa-question-circle fa-lg" data-toggle="tooltip" data-placement="top" title="{{ __('messages.channel_podcast_name_help') }}"></i>
			</label>

			<div class="col-md-10">
				<input type="text" id="podcastName" name="podcast_title" class="form-control" value="{{ $channel->title() }}" placeholder="{{ __('messages.channel_podcast_name_label') }}">
			</div>
		</div>

		<div class="row mb-2">
			<label for="authors" class="col-md-2">
				{{ __('messages.channel_authors_label') }}
				<i class="fas fa-question-circle fa-lg" data-toggle="tooltip" data-placement="top" title="{{ __('messages.channel_authors_help') }}"></i>
			</label>

			<div class="col-md-10">
				<input type="text" class="form-control" id="authors" name="authors" value="{{ $channel->authors }}">
			</div>
		</div>

		<div class="row mb-2">
			<label for="email" class="col-md-2">
				{{ __('messages.channel_email_label') }}
				<i class="fas fa-question-circle fa-lg" data-toggle="tooltip" data-placement="top" title="{{ __('messages.channel_email_help') }}"></i>
			</label>

			<div class="col-md-10">
				<input type="email" class="form-control" id="email" name="email" value="{{ $channel->email }}">
			</div>
		</div>

		<div class="row mb-2">
			<label for="link" class="col-md-2">
				{{ __('messages.channel_link_label') }}
				<i class="fas fa-question-circle fa-lg" data-toggle="tooltip" data-placement="top" title="{{ __('messages.channel_link_help') }}"></i>
			</label>

			<div class="col-md-10">
				<input type="url" class="form-control" id="link" name="link" value="{{ $channel->link }}">
			</div>
		</div>

		<div class="row mb-2">
			<label for="category" class="col-md-2">
				{{ __('messages.channel_category_label') }}
				<i class="fas fa-question-circle fa-lg" data-toggle="tooltip" data-placement="top" title="{{ __('messages.channel_category_help') }}"></i>
			</label>

			<div class="col-md-2">
				<select id="category" name="category_id" class="custom-select">
					@include('partials.categories', ['channelSelectedCategory' => $channel->category_id])
				</select>
			</div>

			<label for="lang" class="col-md-2">
				{{ __('messages.channel_lang_label') }}
				<i class="fas fa-question-circle fa-lg" data-toggle="tooltip" data-placement="top" title="{{ __('messages.channel_lang_help') }}"></i>
			</label>

			<div class="col-md-2">
				<select class="custom-select" id="lang" name="lang">
					<option value="EN" {{ $channel->lang == 'EN' ? ' selected' : '' }}>{{ __("localized.EN") }}</option>
					<option value="FR" {{ $channel->lang == 'FR' ? ' selected' : '' }}>{{ __("localized.FR") }}</option>
					<option value="PT" {{ $channel->lang == 'PT' ? ' selected' : '' }}>{{ __("localized.PT") }}</option>
				</select>
			</div>

			<label for="explicit" class="col-md-3">
				{{ __('messages.channel_explicit_label') }}
				<i class="fas fa-question-circle fa-lg" data-toggle="tooltip" data-placement="top" title="{{ __('messages.channel_explicit_help') }}"></i>
			</label>

			<div class="col-md-1">
				<input type="checkbox" id="explicit" name="explicit" value="1" {{ $channel->explicit == 1 ? 'checked' : ''}}>
			</div>
		</div>

		<div class="mx-auto" style="width:200px">
			<button type="submit" class="btn btn-success">{{ __('messages.button_update_label') }}</button>
			<a href="{{ route('channel.show', $channel->channel_id) }}" class="btn btn-secondary">
				{{ __('messages.button_cancel_label') }}
			</a>
		</div>

		<h3>{{ __('messages.channel_filters_label') }}</h3>
		<div class="alert alert-danger text-center" role="alert">
			{!! __('messages.filters_warning') !!}
		</div>

		<div class="row mb-2">
			<label for="filtertag" class="col-md-2">
				{{ __('messages.channel_filter_by_tag_label') }}
				<i class="fas fa-question-circle fa-lg" data-toggle="tooltip" data-placement="top" title="{{ __('messages.channel_filter_by_tag_help') }}"></i>
			</label>

			<div class="col-md-10">
				<input type="text" class="form-control" id="filtertag" name="accept_video_by_tag" value="{{ $channel->accept_video_by_tag }}">
			</div>
		</div>

		<div class="row mb-2">
			<label for="filterkw" class="col-md-2">
				{{ __('messages.channel_filter_by_keyword_label') }}
				<i class="fas fa-question-circle fa-lg" data-toggle="tooltip" data-placement="top" title="{{ __('messages.channel_filter_by_keyword_help') }}"></i>
			</label>

			<div class="col-md-10">
				<input type="text" class="form-control" id="filterkw" name="reject_video_by_keyword" value="{{ $channel->reject_video_by_keyword }}">
			</div>
		</div>


		<div class="mx-auto" style="width:200px">
			<button type="submit" class="btn btn-success">{{ __('messages.button_update_label') }}</button>
			<a href="{{ route('channel.show', $channel->channel_id) }}" class="btn btn-secondary">
				{{ __('messages.button_cancel_label') }}
			</a>
		</div>

	</form>
</div>
<!--/section container-->
<script type="text/javascript">
	$(document).ready(function() {
		$(function() {
			$('[data-toggle="tooltip"]').tooltip()
		})
	});
</script>

@endsection