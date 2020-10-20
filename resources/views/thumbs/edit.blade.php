@extends('layouts.app')

@section('pageTitle', __('messages.page_title_channel_thumbs_index') )

@section ('content')
<div class="container mx-auto text-white">
    <h2 class="text-3xl md:text-5xl text-white font-semibold">
        Update cover for {{ $channel->title() }}
    </h2>

    <form class="max-w-5xl" id="edit-cover-form" method="POST" action="{{ route('channel.thumbs.store', $channel) }}" enctype="multipart/form-data">
        {{ csrf_field() }}
        <div class="">
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
            <input type="file" name="new_thumb_file" id="new_thumb_file" class="" />
        </div>

        <div class="p-6 max-w-lg flex content-center">
            <a href="#" onclick="event.preventDefault(); document.getElementById('edit-cover-form').submit();">
                <button type="button" id="btnSubmit" class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-900 font-bold py-2 px-4 rounded-l-lg">Update</button>
            </a>
            <a href="{{ route('home') }}">
                <button type="button" class="flex-1 bg-gray-800 hover:bg-gray-700 text-gray-100 font-bold py-2 px-4 rounded-r-lg">Cancel</button>
            </a>
        </div>
    </form>
</div>
@endsection