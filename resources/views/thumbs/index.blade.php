@extends('layouts.app') @section('pageTitle', __('messages.page_title_channel_thumbs_index') )

@section ('content')

{{ Breadcrumbs::render('thumbs.index', $channel) }}

<div class="container h100 text-center">
    <p>
    <a href="{{ route('channel.thumbs.edit', $channel) }}">
        <button type="button" class="btn btn-primary">
            {{ __("messages.button_update_thumbs_label") }}
        </button>
    </a>
    </p>

    <img src="{{ $thumb_url }}" class="img-fluid" style="max-height:600px;" alt="Responsive image" />
</div>

@endsection