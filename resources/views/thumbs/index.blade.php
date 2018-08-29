@extends('layouts.app')

@section('pageTitle', __('messages.page_title_channel_thumbs_index') )

@section ('content')

    {{ Breadcrumbs::render('thumbs.index', $channel) }}

        <div class="container text-center">
            
            <a href="{{ route('channel.thumbs.edit', $channel) }}">
                <button type="button" class="btn btn-primary">
                    {{ __('messages.button_update_thumbs_label') }}
                </button>
            </a>
            
            @if ($thumb_url)
                <img src="{{ $thumb_url }}" class="img-fluid" alt="Responsive image">    
            @else
                <div class="alert alert-primary" role="alert">
                    {{ __('thumbs_messages.your_podcast_has_no_thumb_yet') }}
                </div>
            @endif
            
            
            
            
        </div>
    </div>

@endsection