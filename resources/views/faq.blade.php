@extends('layouts.app')

@section('pageTitle', "All the questions you may ask yourself about podmytube")

@section('content')

<div class="max-w-screen-lg mx-auto px-8 text-xl py-16 text-white">

    <h1 class="text-center text-3xl pb-6 md:text-5xl text-white font-semibold">Frequently Asked Question</h1>

    <section class="w-full p-8">
        <div class="flex flex-col rounded-lg shadow-lg p-8 bg-white text-gray-900 ">
            <div class="flex flex-col">
                <div class="my-2">
                    <div class="flex justify-between">
                        <h2 class="font-semibold text-lg">
                            How will I know what podcast catalogs my podcast is on ?
                        </h2>
                    </div>
                    <p class="text-gray-700">
                        I did not implement any solution to register your feed on any podcast catalog automatically. You will have to register it manually on each of them.<br>
                        Be quiet, this will occur only one time 🤗. So, if you register your feed on Itunes podcast and you talk about it on your channel your podcast will be well underway. 🚀
                    </p>
                </div>

                <div class="my-2">
                    <div class="flex justify-between">
                        <h2 class="font-semibold text-lg">
                            How do I register my feed on iTunes podcast catalog ?
                        </h2>
                    </div>
                    <p class="text-gray-700">
                        The quick answer :
                        <ol class="text-gray-700">
                        <li>First, you need to go to <a href="https://itunesconnect.apple.com/">https://itunesconnect.apple.com/</a> and sign in</li>
                        <li>Once logged in click on "podcasts connect" (most to the right)</li>
                        <li>You should see a form where to paste your feed url (you can get it on the podmytube dashboard)</li>
                        <li>Submit it. Apple is making a quick check (it should be good 😎).</li>
                        <li>Validate it. Apple is doing a manual validation on your feed. Usually, it takes between 24/48 hours.
                        </ol>
                    </p>
                </div>

                <div class="my-2">
                    <div class="flex justify-between">
                        <h2 class="font-semibold text-lg">
                            question
                        </h2>
                    </div>
                    <p class="text-gray-700">
                        answer
                    </p>
                </div>
                
            </div>
        </div>
    </section>

</div>


@endsection