@extends('layouts.app') @section('pageTitle', __('messages.page_title_channel_thumbs_index') )

@section ('content')

{{ Breadcrumbs::render('thumbs.index', $channel) }}

<div class="container text-center">
    <a href="{{ route('channel.thumbs.edit', $channel) }}">
        <button type="button" class="btn btn-primary">
            {{ __("messages.button_update_thumbs_label") }}
        </button>
    </a>

    <img src="{{ $thumb_url }}" height="1400" class="img-fluid" alt="Responsive image" />
</div>

@endsection