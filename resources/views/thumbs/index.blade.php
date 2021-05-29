@extends('layouts.app') @section('pageTitle', __('messages.page_title_channel_thumbs_index') )

@section ('content')

<div class="container h100 text-center">

    <h2>
		Cover for {{ $channel->title() }}
	</h2>

    <p>
    <a href="{{ route('channel.cover.edit', $channel) }}">
        <button type="button" class="btn btn-primary">
            {{ __("messages.button_update_thumbs_label") }}
        </button>
    </a>
    </p>

    <img src="{{ $thumb_url }}" class="img-fluid" style="max-height:600px;" alt="Responsive image" />
</div>

@endsection