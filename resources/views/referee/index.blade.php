@extends('layouts.app')

@section('pageTitle', 'My referees 💰')

@section('content')

    <div class="max-w-screen-xl mx-auto py-6 md:py-12 px-4">
        <h2 class="text-3xl md:text-5xl text-white font-semibold">My referees 💰</h2>

        @if ($referees->count())
            cool
        @else
            <div class="bg-gray-100 border rounded-lg border-gray-500 text-gray-900 my-2 px-4 py-3" role="alert">
                <p class="font-bold">You have no referees at this time.</p>
                <p class="text-base">Here is your <span id="referral_code">{{ $user->referralLink() }}</span>.
                    <button onclick="toClipBoard()"> 📋 </button>
                </p>
            </div>
        @endif

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
