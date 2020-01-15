@extends('layouts.app')

@section('pageTitle', __('messages.page_title_channel_show') . $channel->channel_name )

@section ('content')

{{ Breadcrumbs::render('channel.show', $channel) }}

<div class="container"><!--section container-->

	<h2> 
	{{ $channel->channel_name }}
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
                    </span> : @if ($channel->createdAt()) {{ $channel->createdAt()->format(__('localized.dateFormat')) }} @endif<br/>

					<span class="font-italic">
                        {{ __('messages.channel_podcast_updated_label') }}
                    </span> : @if ($channel->podcastUpdatedAt()) {{  $channel->podcastUpdatedAt()->format(__('localized.dateFormat')) }} @else {{__('channels.never')}} @endif
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
		  <th scope="row" width="25%">
			
				{{ __('messages.channel_name_label') }}

				<a href="#" title="{{ __('messages.channel_name_help') }}"><img src="/images/glyphicons-195-question-sign.png" class="float-right"></a>
			
			</th>
		  <td>
				
				{{ $channel->channel_name }}

			</td>
		</tr>
		<tr>
		  <th scope="row">
			
				{{ __('messages.channel_podcast_name_label') }} 
			
				<a href="#" title="{{ __('messages.channel_podcast_name_help') }}"><img src="/images/glyphicons-195-question-sign.png" class="float-right"></a>
			
			</th>
		  <td>
				@if ($channel->podcast_title)
					{{ $channel->podcast_title }}
				@else
					{{ $channel->channel_name }}
				@endif
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
		{{-- waiting for subcategory part
		<!tr>
		  <th scope="row">{{ __('messages.channel_subcategory_label') }} <a href="#" title="{{ __('messages.channel_subcategory_help') }}"><img src="/images/glyphicons-195-question-sign.png" class="float-right"></a></th>
		  <td>{{ $channel->subcategory }}</td>
		</tr>
		--}}
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

		<h3>FTP</h3>
		<p>{{ __('messages.channel_ftp_feature_description') }}</p>

		<table class="table table-striped">
	  <tbody>
		<tr>
		  <th scope="row" width="25%">
			
				{{ __('messages.channel_ftp_host_label') }} 
				
				<a href="#" title="{{ __('messages.channel_ftp_host_help') }}"><img src="/images/glyphicons-195-question-sign.png" class="float-right"></a>
			
			</th>
		  <td>

					@if ($channel->ftp_host)

						{{ $channel->ftp_host }}

					@else

						{{ __('messages.no_ftp_host_defined_label') }} 

					@endif
			</td>
		</tr>
		<tr>
		  <th scope="row">
			
				{{ __('messages.channel_ftp_user_label') }} 
				
				<a href="#" title="{{ __('messages.channel_ftp_user_help') }}"><img src="/images/glyphicons-195-question-sign.png" class="float-right"></a>
			
			</th>
		  <td>

					@if ($channel->ftp_user)

						{{ $channel->ftp_user }}

					@else

						{{ __('messages.no_ftp_user_defined_label') }} 

					@endif
			</td>
		</tr>
		<tr>
		  <th scope="row">
			
				{{ __('messages.channel_ftp_pass_label') }} 
				
				<a href="#" title="{{ __('messages.channel_ftp_pass_help') }}"><img src="/images/glyphicons-195-question-sign.png" class="float-right"></a>
			
			</th>
		  <td>

					@if ($channel->ftp_pass)

						{{ $channel->ftp_pass }}

					@else

						{{ __('messages.no_ftp_pass_defined_label') }} 

					@endif
			</td>
		</tr>
		<tr>
		  <th scope="row">
			
				{{ __('messages.channel_ftp_dir_label') }} 
				
				<a href="#" title="{{ __('messages.channel_ftp_dir_help') }}"><img src="/images/glyphicons-195-question-sign.png" class="float-right"></a>
			
			</th>
		  <td>

					@if ($channel->ftp_dir)

						{{ $channel->ftp_dir }}

					@else

						{{ __('messages.no_ftp_dir_defined_label') }} 

					@endif
			</td>
		</tr>
		<tr>
		  <th scope="row">
			
				{{ __('messages.channel_ftp_podcast_label') }} 
				
				<a href="#" title="{{ __('messages.channel_ftp_podcast_help') }}"><img src="/images/glyphicons-195-question-sign.png" class="float-right"></a>
			
			</th>
		  <td>

					@if ($channel->ftp_podcast)

						{{ $channel->ftp_podcast }}

					@else

						{{ __('messages.no_ftp_podcast_defined_label') }} 

					@endif
			</td>
		</tr>
		<tr>
		  <th scope="row">
			
				{{ __('messages.channel_ftp_pasv_label') }} 
				
				<a href="#" title="{{ __('messages.channel_ftp_pasv_help') }}"><img src="/images/glyphicons-195-question-sign.png" class="float-right"></a>
			
			</th>
		  <td>

					@if ($channel->ftp_pasv)

						{{ __('messages.ftp_pasv_defined_label') }} 

					@else

						{{ __('messages.no_ftp_pasv_defined_label') }} 

					@endif
			</td>
		</tr>
	  </tbody>
	</table>

</div><!--/section container-->

@endsection
