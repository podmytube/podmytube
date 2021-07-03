@extends('layouts.app', ['var' => 1])

@section('pageTitle', __('messages.page_title_user_show'))

@section('content')

@section('content')
    <div class="max-w-screen-xl mx-auto py-6 md:py-12 px-4">
        <h2 class="text-3xl md:text-5xl text-white font-semibold">Your account</h2>

        <p class="text-sm pb-4 text-gray-100">
            This is the place where you can edit your account informations.
        </p>

        <div class="rounded-lg bg-white text-gray-900 p-4">
            <form method="POST" id="edit-user-form" action="{{ route('user.update', $user) }}">
                {{ method_field('PATCH') }}

                {{ csrf_field() }}

                <div class="pb-4">
                    <label class="block py-1" for="firstname">Your firstname</label>
                    <input type="text" id="firstname" name="firstname" value="{{ old('firstname') ?? $user->firstname }}"
                        placeholder="Your firstname" aria-label="Your firstname"
                        class="w-full px-5 py-1 text-gray-900 bg-gray-200 rounded placeholder-gray-700">
                </div>

                <div class="pb-4">
                    <label class="block py-1" for="lastname">Your lastname</label>
                    <input type="text" id="lastname" name="lastname" value="{{ old('lastname') ?? $user->lastname }}"
                        placeholder="Your lastname" aria-label="Your lastname"
                        class="w-full px-5 py-1 text-gray-900 bg-gray-200 rounded placeholder-gray-700">
                </div>

                <div class="pb-4">
                    <label class="block py-1" for="email">Your email</label>
                    <input type="email" id="email" name="email" value="{{ old('email') ?? $user->email }}"
                        placeholder="Your email" aria-label="Your email"
                        class="w-full px-5 py-1 text-gray-900 bg-gray-200 rounded placeholder-gray-700">
                </div>

                <div class="pb-4 pl-7">
                    <label class="block py-1" for="newsletter">
                        <input type="checkbox" id="newsletter" name="newsletter" value="1" class="form-checkbox" @if ($user->newsletter) checked @endif>
                        <span class="ml-2">I agree to the receive <span class="underline">some emails</span></span><br>
                        <small class="pl-4">the only emails you will receive are monthly reports and (more rarely) important
                            news about Podmytube</small>
                    </label>
                </div>

                <div class="flex mt-4 justify-center items-center">
                    <a href="#" onclick="event.preventDefault(); document.getElementById('edit-user-form').submit();">
                        <button type="submit"
                            class="flex-1 bg-gray-800 text-gray-100 hover:bg-gray-700 font-bold py-2 px-4 rounded-l-lg">Submit</button>
                    </a>
                    <a href="{{ route('home') }}">
                        <button type="button"
                            class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-900 font-bold py-2 px-4 rounded-r-lg">Cancel</button>
                    </a>
                </div>

                {{-- <div class="w-3/4 mx-auto pb-6 px-6 rounded-lg border-2 border-red-700 bg-red-200 text-center ">
                    <div class="p-4 font-semibold">
                        <strong>💣 Danger zone 💥</strong><br>
                        change only if you know what you are doing ! 🔥
                    </div>
                    I only want to include videos with the
                    <input type="text" id="accept_video_by_tag" name="accept_video_by_tag"
                        value="{{ $channel->accept_video_by_tag }}"
                        class="px-5 py-1 text-gray-900 bg-gray-200 rounded placeholder-gray-700">
                    tag
                </div>

                <div class="flex mt-4 justify-center items-center">
                    <a href="#" onclick="event.preventDefault(); document.getElementById('edit-podcast-form').submit();">
                        <button type="submit"
                            class="flex-1 bg-gray-800 text-gray-100 hover:bg-gray-700 font-bold py-2 px-4 rounded-l-lg">Submit</button>
                    </a>
                    <a href="{{ route('home') }}">
                        <button type="button"
                            class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-900 font-bold py-2 px-4 rounded-r-lg">Cancel</button>
                    </a>
                </div> --}}
            </form>
        </div>
    </div>
@endsection
