@extends('layouts.app')

@section('pageTitle', __('messages.page_title_channel_create'))

@section ('content')

<div class="container mx-auto text-white">
    
    <h2 class="text-3xl md:text-5xl text-white font-semibold">Add a podcast</h2>

    <form class="max-w-5xl" id="create-podcast-form" method="POST" action="{{ route('channel.store') }}">
        @csrf

        <div class="flex flex-wrap mb-6 w-full">
            <div class="px-3">
                <label class="block uppercase tracking-wide text-white text-xs font-bold mb-2" for="channel_url">
                    Please type the url of the channel you want to transform into a beautiful podcast.
                </label>
                <input type="text" id="channel_url" placeholder="https://www.youtube.com/channel/UCVeMw72tepFl1Zt5fvf9QKQ" required
                    class="block bg-gray-200 w-full text-gray-900 border border-gray-200 rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                    value="{{ old('channel_url') ?? '' }}">
                <p class="text-gray-100 text-xs italic">Enter the full address of your youtube channel</p>
            </div>
        </div>

        <div class="md:flex md:items-center mb-6">
            <div class="md:w-1/3"></div>
            <label for="owner" class="md:w-2/3 block text-gray-500 font-bold">
                <input class="mr-2 leading-tight" type="checkbox" id="owner" name="owner" value="1">
                <span class="text-sm">
                    I swear to be the owner of this channel before checking this box
                </span>
            </label>
        </div>
        
        <div class="max-w-lg flex content-center">
            <a href="#" onclick="event.preventDefault(); document.getElementById('create-podcast-form').submit();">
                <button type="submit" class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-900 font-bold py-2 px-4 rounded-l-lg">Submit</button>
            </a>
            <a href="{{ route('home') }}">
                <button type="button" class="flex-1 bg-gray-800 hover:bg-gray-700 text-gray-100 font-bold py-2 px-4 rounded-r-lg">Cancel</button>
            </a>
        </div>
    </form>
</div>

@endsection