@extends('layouts.app')

@section('pageTitle', __('messages.page_title_channel_thumbs_index') )

@section ('content')

    {{ Breadcrumbs::render('thumbs.edit', $channel) }}

    <div class="container" style="margin:0 auto;width:80%">

    @include ('layouts.errors')

    <form method="POST" action="/channel/{{ $channel->channel_id }}/thumbs" enctype="multipart/form-data">

        {{ csrf_field() }}

        <div class="form-group">

            <p>
            {!! __('messages.thumbs_edit_new_thumb_help_message') !!}
            </p>

            <label for="new_thumb_file">{{__('messages.thumbs_edit_new_thumb_form_label')}}</label><br/>

            <input type="file" name="new_thumb_file" id="new_thumb_file" />
        
        </div>

        <div class="mx-auto" style="width:200px">
            
            <button type="submit" class="btn btn-primary">{{ __('messages.button_update_label') }}</button>
            
            <a href="{{ route('channel.thumbs.index', $channel->channel_id) }}" class="btn btn-secondary">
                {{ __('messages.button_cancel_label') }}
            </a>

		</div>
    </form>    
    </div>

@endsection