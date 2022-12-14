@extends('layouts.app', ['var' => 1])

@section('pageTitle', 'User informations')

@section('content')
    <div class="max-w-screen-xl mx-auto py-6 md:py-12 px-4">
        <h2 class="text-3xl md:text-5xl text-white font-semibold">Your account 💬</h2>

        <p class="text-sm pb-4 text-gray-100">
            This is the place where you can edit your account informations.
        </p>

        <div class="bg-gray-100 my-2 p-4 rounded-lg shadow">
            Your referral link : <span id="referral_code">{{ $user->referralLink() }}</span>
            <button onclick="toClipBoard()"> 📋 </button>
        </div>

        <div class="rounded-lg bg-white text-gray-900 p-4">
            <form method="POST" id="edit-user-form" action="{{ route('user.update', $user) }}">
                {{ method_field('PATCH') }}

                {{ csrf_field() }}

                <div class="pb-4">
                    <label class="block py-1" for="firstname">Your firstname *</label>
                    <input type="text" id="firstname" name="firstname" value="{{ old('firstname', $user->firstname) }}"
                        placeholder="Your firstname" aria-label="Your firstname"
                        class="w-full px-5 py-1 text-gray-900 bg-gray-200 rounded placeholder-gray-700">
                </div>

                <div class="pb-4">
                    <label class="block py-1" for="lastname">Your lastname *</label>
                    <input type="text" id="lastname" name="lastname" value="{{ old('lastname', $user->lastname) }}"
                        placeholder="Your lastname" aria-label="Your lastname"
                        class="w-full px-5 py-1 text-gray-900 bg-gray-200 rounded placeholder-gray-700">
                </div>

                <div class="pb-4">
                    <label class="block py-1" for="email">Your email</label>
                    <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}"
                        placeholder="Your email" aria-label="Your email"
                        class="w-full px-5 py-1 text-gray-900 bg-gray-200 rounded placeholder-gray-700">
                </div>

                <div class="pb-4 pl-7">
                    <label class="block py-1" for="newsletter">
                        <input type="checkbox" id="newsletter" name="newsletter" value="1" class="form-checkbox"
                            @if ($user->newsletter) checked @endif>
                        <span class="ml-2">I agree to the receive <span class="underline">some emails</span></span><br>
                        <small class="pl-4">the only emails you will receive are monthly reports and (more rarely)
                            important
                            news about Podmytube</small>
                    </label>
                </div>

                <div class="pb-4 pl-7">
                    <label class="block py-1" for="dont_warn_exceeding_quota">
                        <input type="checkbox" id="dont_warn_exceeding_quota" name="dont_warn_exceeding_quota"
                            value="1" class="form-checkbox" @if ($user->dont_warn_exceeding_quota) checked @endif>
                        <span class="ml-2">Don't warn me for exceeding quota.</span><br>
                        <small class="pl-4">You won't be warned for exceeding quota</small>
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
            </form>
        </div>

        <div class="mt-4 p-4">
            <form method="POST" id="delete-user-form" action="{{ route('user.destroy', $user) }}">
                @method('DELETE')
                @csrf
                <div class="w-3/4 mx-auto pb-6 px-6 rounded-lg border-8 border-red-700 bg-white text-center ">
                    <div class="p-4 font-semibold border-2 border-red-700 rounded bg-red-200 mt-4">
                        <strong>💀 DEAD zone ❌</strong><br>
                    </div>

                    <div class="p-4 text-red-800">
                        So you want to delete your account ?<br>
                        <span class="underline">Once clicked</span> on the button below, all of your data will <span
                            class="underline">be permanently removed</span>.<br>
                        <strong>Do. or do not. There is no try</strong>.<br>
                        This action cannot be undone !
                    </div>

                    <div class="flex mt-4 justify-center items-center">
                        <a href="#"
                            onclick="event.preventDefault(); document.getElementById('delete-user-form').submit();">
                            <button type="submit"
                                class="flex-1 bg-red-800 text-gray-100 hover:bg-red-700 font-bold py-2 px-4 rounded-lg">Delete
                                my account !</button>
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script type="text/javascript">
        function toClipBoard() {
            // Get the text field
            var referralCodeItem = document.getElementById("referral_code");

            var referralCodeLink = referralCodeItem.innerHTML;

            copyToClipboard(referralCodeLink);

            alert("Referral link copied : " + referralCodeLink);
        }

        // return a promise
        function copyToClipboard(textToCopy) {
            // navigator clipboard api needs a secure context (https)
            if (navigator.clipboard && window.isSecureContext) {
                // navigator clipboard api method'
                return navigator.clipboard.writeText(textToCopy);
            } else {
                // text area method
                let textArea = document.createElement("textarea");
                textArea.value = textToCopy;
                // make the textarea out of viewport
                textArea.style.position = "fixed";
                textArea.style.left = "-999999px";
                textArea.style.top = "-999999px";
                document.body.appendChild(textArea);
                textArea.focus();
                textArea.select();
                return new Promise((res, rej) => {
                    // here the magic happens
                    document.execCommand('copy') ? res() : rej();
                    textArea.remove();
                });
            }
        }
    </script>
@endpush
