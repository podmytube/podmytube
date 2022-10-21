@extends('layouts.app')

@section('pageTitle', 'All the questions you may ask yourself about podmytube')

@section('content')

    <div class="max-w-screen-lg mx-auto px-8 text-xl py-16 text-white">

        <h1 class="text-center text-3xl pb-6 md:text-5xl text-white font-semibold">Frequently Asked Question 📋</h1>

        <div class="flex flex-col rounded-lg shadow-lg p-8 bg-white text-gray-900 ">
            <div class="flex flex-col">
                <div class="py-4">
                    <h2 class="font-semibold text-lg">
                        How will I know what podcast catalogs my podcast is on ?
                    </h2>
                    <p class="text-gray-700 py-2 text-base">
                        I did not implement any solution to register your feed on any podcast catalog automatically. You
                        will have to register it manually on each of them.<br>
                        Be quiet, this will occur only one time 🤗. So, if you register your feed on Itunes podcast and you
                        talk about it on your channel your podcast will be well underway. 🚀
                    </p>
                </div>

                <div class="py-4">
                    <h2 class="font-semibold text-lg">
                        How do I register my feed on iTunes podcast catalog ?
                    </h2>
                    <p class="text-gray-700 text-base">
                        The quick answer :
                    <ol class="text-gray-700 list-decimal pl-8 text-base">
                        <li>First, you need to go to <a
                                href="https://itunesconnect.apple.com/">https://itunesconnect.apple.com/</a> and sign in
                        </li>
                        <li>Once logged in click on "podcasts connect" (most to the right)</li>
                        <li>You should see a form where to paste your feed url (you can get it on the podmytube dashboard)
                        </li>
                        <li>Submit it. Apple is making a quick check (it should be good 😎).</li>
                        <li>Validate it. Apple is doing a manual validation on your feed. Usually, it takes between 24/48
                            hours.</li>
                    </ol>
                    </p>
                </div>

                <div class="py-4">
                    <h2 class="font-semibold text-lg"> How do I listen to my podcast ? </h2>
                    <p class="text-gray-700 py-2 text-base">
                        Once created, you can get the feed url of your podcast from the dashboard. <br>
                        Your feed url is something like <strong>https://podcasts.podmytube.com/...</strong>.<br>
                        Then, the easiest way to listen to it is to copy this feed url and paste it into your favorite
                        podcast app.<br>
                        Within Apple Podcast you are looking for "Add a show by URL" for example.
                    </p>
                </div>

                <div class="py-4">
                    <h2 class="font-semibold text-lg"> Why aren't all previous episodes available ? </h2>
                    <p class="text-gray-700 py-2 text-base">
                        Each plan has its own number of episode to begin with :
                    </p>
                    <ul class="text-gray-700 list-disc pl-8 text-base">
                        <li>Free plan start with the last episode published on Youtube and paid plans are including various
                            number of episode according to the plan itself.</li>
                        <li>Starter will begin with the five last episodes.</li>
                        <li>Professional will begin with the 12 last episodes.</li>
                        <li>Business will begin with the 33 last episodes.</li>
                    </ul>
                    <p class="text-gray-700 py-2 text-base">
                        If it's not enough and you absolutely want me to include all your episodes from the birth of your
                        channel, please <a href="mailto:contact@podmytube.com" class="underline">contact me</a>.
                    </p>
                </div>

                <div class="py-4">
                    <h2 class="font-semibold text-lg"> Can I choose which episode to go out ? </h2>
                    <p class="text-gray-700 py-2 text-base">
                        Yes, you can choose which episode will be available in you podcast.<br>
                        The way to to this is simple :
                    </p>
                    <ol class="text-gray-700 list-decimal pl-8 text-base">
                        <li>From Youtube Studio, click on the "details" link of the video you want to include</li>
                        <li>All way down this page, click on the "MORE" link (below the form 'May children watch this')</li>
                        <li>In the Tags form, add a new tag (let's say "podcast") and save</li>
                        <li>Once done for all the video you want to include, go back to the <a href="{{ route('home') }}"
                                class="underline">dashboard</a></li>
                        <li>Edit your channel</li>
                        <li>Fill the blank with the same tag in the sentence "I only want to include videos with the [....]
                            tag". Save and its done</li>
                    </ol>
                </div>

                <div class="py-4">
                    <h2 class="font-semibold text-lg">
                        Are shorts included in my podcast ?
                    </h2>
                    <p class="text-gray-700 py-2 text-base">
                        Yes.
                    </p>
                </div>

                <div class="my-2">
                    <h2 class="font-semibold text-lg">Where to get my referral link.</h2>
                    <p class="text-gray-700 py-2 text-base"> Once registered and your email address verified you will find
                        it on the profile page. </p>
                </div>

                <div class="my-2">
                    <h2 class="font-semibold text-lg">How many referrals can I have.</h2>
                    <p class="text-gray-700 py-2 text-base"> Sky is the limit. You can have as many referrals as you want.
                    </p>
                </div>

                <div class="my-2">
                    <h2 class="font-semibold text-lg">How many levels of referrals may I have.</h2>
                    <p class="text-gray-700 py-2 text-base"> There is only one level of referrals.
                        If one of your referral is becoming referrer too, you won't gain anything on his/her referrals.
                    </p>
                </div>

                <!--div class="my-2">
                                        <h2 class="font-semibold text-lg"> question </h2>
                                        <p class="text-gray-700 py-2 text-base"> answer </p>
                                    </div-->

            </div>
        </div>

    </div>


@endsection
