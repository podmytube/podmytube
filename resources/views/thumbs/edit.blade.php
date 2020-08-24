@extends('layouts.app')

@section('pageTitle', __('messages.page_title_channel_thumbs_index') )

@section ('content')

<div class="container" style="margin:0 auto;width:80%">
    <h2>
		Update cover for {{ $channel->title() }}
	</h2>

    <form method="POST" action="{{ route('channel.thumbs.store', $channel) }}" enctype="multipart/form-data">
        {{ csrf_field() }}
        <div class="form-group">
            <p>
                <b>Image should meet iTunes requirements !</b> 
                <ul>
                    <li>Minimum dimensions : 1400x1400</li>
                    <li>Maximum dimensions : 3000x3000</li>
                    <li>Squared : width = height</li>
                    <li>File size must be less than 5Mb (using jpg format will help)</li>
                </ul>
            </p>
            <label for="new_thumb_file">{{__('messages.thumbs_edit_new_thumb_form_label')}}</label><br />
            <input type="file" name="new_thumb_file" id="new_thumb_file" />
        </div>

        <div class="mx-auto" style="width:200px">
            <button type="submit" id="btnSubmit" class="btn btn-primary">{{ __('messages.button_update_label') }}</button>
            <a href="{{ route('channel.thumbs.index', $channel->channel_id) }}" class="btn btn-secondary">
                {{ __('messages.button_cancel_label') }}
            </a>
        </div>
    </form>
</div>
@endsection