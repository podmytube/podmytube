@extends('layouts.app')

@section('pageTitle', __('messages.page_title_channel_create'))

@section ('content')

<div class="container mx-auto text-white">
    
    <h2 class="text-3xl md:text-5xl text-white font-semibold">Add a podcast</h2>

    <form class="max-w-5xl" method="POST" action="{{ route('channel.store') }}">
        @csrf

        <div class="flex flex-wrap mb-6 w-full">
            <div class="px-3">
                <label class="block uppercase tracking-wide text-white text-xs font-bold mb-2" for="channel_url">
                    The channel you want to transform into a beautiful podcast.
                </label>
                <input type="text" id="channel_url" placeholder="https://www.youtube.com/channel/UCVeMw72tepFl1Zt5fvf9QKQ" required
                    class="block bg-gray-200 w-full text-gray-900 border border-gray-200 rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                    value="{{ old('channel_url') ?? '' }}">
                <p class="text-gray-100 text-xs italic">Enter the full address of your youtube channel</p>
            </div>
        </div>

        <div class="flex flex-wrap mb-6 w-full">
            <label for="owner" class="form-check-label mx-4">
            I solemnly swear to be the owner of this channel before checking this box
            </label>
            <input type="checkbox" id="owner" name="owner" value="1" required
                class="appearance-none checked:bg-gray-900 checked:border-transparent">
        </div>
        
        <div class="text-center">
            <button type="submit" class="">Submit</button>
            <button type="cancel" class="">Cancel</button>
        </div>
    </form>
</div>

@endsection