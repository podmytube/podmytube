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

	@include ('partials.errors')

	<form method="POST" action="/channel/{{ $channel->channel_id }}">

		{{ method_field('PATCH') }}

		{{ csrf_field() }}

		<div class="row mb-2">
			<label for="podcastName" class="col-md-2">
				{{ __('messages.channel_podcast_name_label') }}
				<i class="fas fa-question-circle fa-lg" data-toggle="tooltip" data-placement="top" title="{{ __('messages.channel_podcast_name_help') }}"></i>
			</label>

			<div class="col-md-10">
				<input type="text" id="podcastName" name="podcast_title" class="form-control" placeholder="{{ __('messages.channel_podcast_name_label') }}">
			</div>
		</div>

		<div class="row mb-2">
			<label for="authors" class="col-md-2">
				{{ __('messages.channel_authors_label') }}
				<i class="fas fa-question-circle fa-lg" data-toggle="tooltip" data-placement="top" title="{{ __('messages.channel_authors_help') }}"></i>
			</label>

			<div class="col-md-10">
				<input type="text" class="form-control" id="authors" name="authors" value="{{ $channel->authors }}" required>
			</div>
		</div>

		<div class="row mb-2">
			<label for="email" class="col-md-2">
				{{ __('messages.channel_email_label') }}
				<i class="fas fa-question-circle fa-lg" data-toggle="tooltip" data-placement="top" title="{{ __('messages.channel_email_help') }}"></i>
			</label>

			<div class="col-md-10">
				<input type="email" class="form-control" id="email" name="email" value="{{ $channel->email }}" required>
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
					<option value="FR" {{ $channel->lang == 'FR' ? ' selected' : '' }}>Français</option>
					<option value="EN" {{ $channel->lang == 'EN' ? ' selected' : '' }}>English</option>
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

		
		<hr>

{{--
		<h3> FTP </h3>
		<p>{{ __('messages.channel_ftp_feature_description') }}</p>

		<table class="table table-striped">
			<tbody>
				<tr>
					<th scope="row" width="25%">

						{{ __('messages.channel_ftp_host_label') }}

						<a href="#" title="{{ __('messages.channel_ftp_host_help') }}"><img src="/images/glyphicons-195-question-sign.png" class="float-right"></a>

					</th>
					<td>

						<input type="text" class="form-control" name="ftp_host" value="{{ $channel->ftp_host }}">

					</td>
				</tr>
				<tr>
					<th scope="row">

						{{ __('messages.channel_ftp_user_label') }}

						<a href="#" title="{{ __('messages.channel_ftp_user_help') }}"><img src="/images/glyphicons-195-question-sign.png" class="float-right"></a>

					</th>
					<td>

						<input type="text" class="form-control" name="ftp_user" value="{{ $channel->ftp_user }}">

					</td>
				</tr>
				<tr>
					<th scope="row">

						{{ __('messages.channel_ftp_pass_label') }}

						<a href="#" title="{{ __('messages.channel_ftp_pass_help') }}"><img src="/images/glyphicons-195-question-sign.png" class="float-right"></a>

					</th>
					<td>

						<input type="password" class="form-control" name="ftp_pass" value="{{ $channel->ftp_pass }}">

					</td>
				</tr>
				<tr>
					<th scope="row">

						{{ __('messages.channel_ftp_dir_label') }}

						<a href="#" title="{{ __('messages.channel_ftp_dir_help') }}"><img src="/images/glyphicons-195-question-sign.png" class="float-right"></a>

					</th>
					<td>

						<input type="text" class="form-control" name="ftp_dir" value="{{ $channel->ftp_dir }}">

					</td>
				</tr>
				<tr>
					<th scope="row">

						{{ __('messages.channel_ftp_podcast_label') }}

						<a href="#" title="{{ __('messages.channel_ftp_podcast_help') }}"><img src="/images/glyphicons-195-question-sign.png" class="float-right"></a>

					</th>
					<td>

						<input type="text" class="form-control" name="ftp_podcast" value="{{ $channel->ftp_podcast }}">

					</td>
				</tr>
				<tr>
					<th scope="row">

						{{ __('messages.channel_ftp_pasv_label') }}

						<a href="#" title="{{ __('messages.channel_ftp_pasv_help') }}"><img src="/images/glyphicons-195-question-sign.png" class="float-right"></a>

					</th>
					<td>

						<input type="checkbox" name="ftp_pasv" value="1" {{ $channel->ftp_pasv == 1 ? 'checked' : ''}}>

					</td>
				</tr>
			</tbody>
		</table>
--}}
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