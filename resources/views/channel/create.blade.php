@extends('layouts.app')

@section('pageTitle', __('messages.page_title_channel_create'))

@section ('content')

<div class="container"><!--section container-->

	<h2> 
    
    	{{ __('messages.page_title_channel_create') }}

	</h2>

	<hr> 

    <div class="container w-60 mt-2 mb-2"> <!--form container-->

    	<form method="POST" action="/channel">

            {{ csrf_field() }}

            <div class="form-group">
                <label for="channel_url">{{ __('messages.youtube_channel_url_label') }}</label>
                <a href="#" title="{{ __('messages.create_youtube_channel_url_help') }}">
                    <img src="/images/glyphicons-195-question-sign.png" class="float-right">
                </a>
                <input id="channel_url" type="text" class="form-control" name="channel_url" required>
            </div>

            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="owner" value="1" required>
                <div class="alert alert-warning">
                    {{ __('messages.channel_owner_warning_checkbox_label') }}
                </div>              
            </div>
        
            <div class="form-group text-center">
                <button type="submit" class="btn btn-primary">{{ __('messages.button_submit_label') }}</button>
            </div>
        </form>

    </div> <!--/form container-->

</div><!--/section container-->

@endsection
