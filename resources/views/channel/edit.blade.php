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

		<div class="row">
			<div class="col">
				<label for="podcastName">{{ __('messages.channel_podcast_name_label') }}</label>
				<a href="#" data-toggle="tooltip" data-placement="top" title="{{ __('messages.channel_podcast_name_help') }}">
					<i class="fas fa-question-circle"></i>
				</a>
				<input type="text" id="podcastName" class="form-control" placeholder="{{ __('messages.channel_podcast_name_label') }}">
			</div>
		</div>
		<table class="table table-striped">
			<tbody>
				<tr>
					<th scope="row" width="25%" class="justify-content-between">

						{{ __('messages.channel_name_label') }}
						<a href="#" data-toggle="tooltip" data-placement="top" title="{{ __('messages.channel_name_help') }}">
							<i class="fas fa-question-circle"></i>
						</a>

					</th>
					<td>{{ $channel->channel_name }}</td>
				</tr>
				<tr>
					<th scope="row">

						{{ __('messages.channel_podcast_name_label') }}

						<a href="#" title="{{ __('messages.channel_podcast_name_help') }}"><img src="/images/glyphicons-195-question-sign.png" class="float-right"></a>

					</th>
					<td>
						<input type="text" class="form-control" name="podcast_title" value="{{ $channel->podcast_title }}">
					</td>
				</tr>
				<tr>
					<th scope="row">

						{{ __('messages.channel_authors_label') }}

						<a href="#" title="{{ __('messages.channel_authors_help') }}"><img src="/images/glyphicons-195-question-sign.png" class="float-right"></a>

					</th>
					<td>
						<input type="text" class="form-control" name="authors" value="{{ $channel->authors }}" required>
					</td>
				</tr>
				<tr>
					<th scope="row">

						{{ __('messages.channel_email_label') }}

						<a href="#" title="{{ __('messages.channel_email_help') }}"><img src="/images/glyphicons-195-question-sign.png" class="float-right"></a>

					</th>
					<td>
						<input type="email" class="form-control" name="email" value="{{ $channel->email }}" required>
					</td>
				</tr>
				<tr>
					<th scope="row">

						{{ __('messages.channel_description_label') }}

						<a href="#" title="{{ __('messages.channel_description_help') }}"><img src="/images/glyphicons-195-question-sign.png" class="float-right"></a>

					</th>
					<td>{{ $channel->description }}</td>
				</tr>
				<tr>
					<th scope="row">

						{{ __('messages.channel_link_label') }}

						<a href="#" title="{{ __('messages.channel_link_help') }}"><img src="/images/glyphicons-195-question-sign.png" class="float-right"></a>

					</th>
					<td>
						<input type="url" class="form-control" name="link" value="{{ $channel->link }}">
					</td>
				</tr>
				<tr>
					<th scope="row">

						{{ __('messages.channel_category_label') }}

						<a href="#" title="{{ __('messages.channel_category_help') }}"><img src="/images/glyphicons-195-question-sign.png" class="float-right"></a>

					</th>
					<td>
						<select name="category_id" class="custom-select">
							@include('partials.categories', ['channelSelectedCategory' => $channel->category_id])
						</select>

					</td>
				</tr>
				<tr>
					<th scope="row">

						{{ __('messages.channel_lang_label') }}

						<a href="#" title="{{ __('messages.channel_lang_help') }}"><img src="/images/glyphicons-195-question-sign.png" class="float-right"></a>

					</th>
					<td>
						<select class="custom-select" name="lang">
							<option value="FR" {{ $channel->lang == 'FR' ? ' selected' : '' }}>FR</option>
							<option value="EN" {{ $channel->lang == 'EN' ? ' selected' : '' }}>EN</option>
						</select>
					</td>
				</tr>
				<tr>
					<th scope="row">

						{{ __('messages.channel_explicit_label') }}

						<a href="#" title="{{ __('messages.channel_explicit_help') }}"><img src="/images/glyphicons-195-question-sign.png" class="float-right"></a>

					</th>
					<td>
						<input type="checkbox" name="explicit" value="1" {{ $channel->explicit == 1 ? 'checked' : ''}}>
					</td>
				</tr>
				<tr>
					<th scope="row">

						{{ __('messages.channel_filter_by_tag_label') }}

						<a href="#" title="{{ __('messages.channel_filter_by_tag_help') }}"><img src="/images/glyphicons-195-question-sign.png" class="float-right"></a>

					</th>
					<td>

						<input type="text" class="form-control" name="accept_video_by_tag" value="{{ $channel->accept_video_by_tag }}">

					</td>
				</tr>
				<tr>
					<th scope="row">

						{{ __('messages.channel_filter_by_keyword_label') }}

						<a href="#" title="{{ __('messages.channel_filter_by_keyword_help') }}"><img src="/images/glyphicons-195-question-sign.png" class="float-right"></a>

					</th>
					<td>

						<input type="text" class="form-control" name="reject_video_by_keyword" value="{{ $channel->reject_video_by_keyword }}">

					</td>
				</tr>
			</tbody>
		</table>


		<hr>

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

		<div class="mx-auto" style="width:200px">
			<button type="submit" class="btn btn-primary">{{ __('messages.button_update_label') }}</button>
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