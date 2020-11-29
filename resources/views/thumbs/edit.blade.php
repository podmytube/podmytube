@extends('layouts.app')

@section('pageTitle', "Your podcast deserve a great cover !" )

@section ('content')
<div class="max-w-screen-xl mx-auto py-6 md:py-12 px-4 text-gray-900">
    <h2 class="text-3xl md:text-5xl text-white font-semibold">
        Update cover for {{ $channel->title() }}
    </h2>

    <div class="bg-gray-100 mt-6 px-4 py-4 rounded-lg">
        <form class="max-w-5xl" id="edit-cover-form" method="POST" action="{{ route('channel.thumbs.store', $channel) }}" enctype="multipart/form-data">
            {{ csrf_field() }}
            <p>
                <b>Image should meet iTunes requirements !</b> 
                <ul class="list-disc py-4">
                    <li class="ml-4">Minimum dimensions : 1400x1400</li>
                    <li class="ml-4">Maximum dimensions : 3000x3000</li>
                    <li class="ml-4">Squared : width = height</li>
                    <li class="ml-4">File size must be less than 5Mb (using jpg format will help)</li>
                </ul>
            </p>
            
            <div class="text-center">
                <label for="new_thumb_file">{{__('messages.thumbs_edit_new_thumb_form_label')}}</label><br />
                <input type="file" name="new_thumb_file" id="new_thumb_file" class="" />
            </div>

            <div class="p-6 flex justify-center items-center">
                <a href="#" onclick="event.preventDefault(); document.getElementById('edit-cover-form').submit();">
                    <button type="button" id="btnSubmit" class="flex-1 bg-gray-800 hover:bg-gray-700 text-gray-100 font-bold py-2 px-4 rounded-l-lg">Update</button>
                </a>
                <a href="{{ route('home') }}">
                    <button type="button" class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-900 font-bold py-2 px-4 rounded-r-lg">Cancel</button>
                </a>
            </div>
        </form>
    </div>
</div>
@endsection