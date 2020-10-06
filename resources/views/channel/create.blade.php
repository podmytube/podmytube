@extends('layouts.app')

@section('pageTitle', __('messages.page_title_channel_create'))

@section ('content')

<div class="container">
    <!--section container-->

    <h2>

        {{ __('messages.page_title_channel_create') }}

    </h2>

    <hr>

    <div class="container w-60 mt-2 mb-2">
        <!--form container-->

        <form method="POST" action="{{ route('channel.store') }}">

            @csrf

            <div class="form-group">
                <label for="channel_url">{{ __('messages.youtube_channel_url_label') }}</label>
                <input id="channel_url" type="text" class="form-control" name="channel_url" required>
            </div>

            <div class="form-check alert alert-info">
                <input class="form-check-input ml-1" type="checkbox" id="owner" name="owner" value="1" required>
                <label for="owner" class="form-check-label mx-4">
                    {{ __('messages.channel_owner_warning_checkbox_label') }}
                </label>
            </div>

            <div class="form-group text-center">
                <button type="submit" class="btn btn-primary">{{ __('messages.button_submit_label') }}</button>
            </div>
        </form>

    </div>
    <!--/form container-->

</div>
<!--/section container-->

@endsection