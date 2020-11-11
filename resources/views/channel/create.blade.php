@extends('layouts.app')

@section('pageTitle', __('messages.page_title_channel_create'))

@section ('content')

<div class="px-2 pt-4 pb-6 sm:px-6 text-gray-900">
    <h2 class="text-3xl md:text-5xl text-white font-semibold">Add a podcast</h2>
    
    <div class="bg-gray-100 mt-6 px-4 py-4 rounded-lg">

        <form class="max-w-5xl" id="create-podcast-form" method="POST" action="{{ route('channel.store') }}">
            @csrf

            <div class="flex flex-wrap">
                <label class="block tracking-tight text-md font-bold mt-2 mb-4" for="channel_url">
                    Please type the url of the channel you want to transform into a beautiful podcast.
                </label>
                <input type="text" id="channel_url" name="channel_url" placeholder="https://www.youtube.com/channel/UCVeMw72tepFl1Zt5fvf9QKQ" required
                    class="block bg-gray-300 w-full border border-gray-200 rounded py-3 px-2 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                    value="{{ old('channel_url') ?? '' }}">
                <p class="text-gray-100 text-xs italic">Enter the full address of your youtube channel</p>
            </div>

            <div class="md:flex md:items-center mb-6">
                <label for="owner" class="md:w-2/3 block text-gray-800 font-bold">
                    <input class="mr-2 leading-tight" type="checkbox" id="owner" name="owner" value="1">
                    <span class="text-sm">
                        I swear to be the owner of this channel before checking this box
                    </span>
                </label>
            </div>
            
            <div class="flex items-center justify-center">
                <a href="#" onclick="event.preventDefault(); document.getElementById('create-podcast-form').submit();">
                    <button type="submit" class="flex-1 bg-gray-800 hover:bg-gray-700 text-gray-100 font-bold py-2 px-4 rounded-l-lg">Submit</button>
                </a>
                <a href="{{ route('home') }}">
                    <button type="button" class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-900 font-bold py-2 px-4 rounded-r-lg">Cancel</button>
                </a>
            </div>
        </form>
    </div>
</div>

@endsection