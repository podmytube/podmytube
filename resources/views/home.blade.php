@extends('layouts.app')

@section('pageTitle', __('messages.page_title_home_index'))


@section('content')
    <div class="max-w-screen-xl mx-auto text-gray-100 py-12 px-4">
        @if (!$user->hasVerifiedEmail() and session()->missing('verification_sent'))
            <div class="bg-gray-100 border rounded-lg border-gray-500 text-gray-900 px-4 py-3 my-2" role="alert">
                <p class="text-center p-2">
                    Your podcast will only be generated once your email address is validated
                </p>

                <div class="flex items-center justify-center bg-gray-100">
                    <form action="{{ route('verification.send') }}" method="POST">
                        @csrf
                        <button type="submit"
                            class="flex-1 bg-gray-800 hover:bg-gray-700 text-gray-100 font-bold py-2 px-4 rounded-lg">
                            Resend verification email
                        </button>
                    </form>
                </div>
            </div>
        @endif
        <div class="flex flex-col sm:flex-row sm:items-start">
            <div class="flex-1">
                @include ('partials.channels')
            </div>
            <div class="flex-1">
                @include ('partials.playlists')
            </div>
        </div>
    </div>
    <!--/home main container-->
@endsection
