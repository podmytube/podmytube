@extends ('layouts.app')

@section('pageTitle', __('messages.page_title_channel_edit') . $channel->channel_name)

@section('content')

    <div class="max-w-screen-xl mx-auto py-6 md:py-12 px-4">
        <h2 class="text-3xl md:text-5xl text-white font-semibold">{{ $channel->channel_name }}</h2>

        <p class="text-sm pb-4 text-gray-100">
            This is the place where you can edit your podcast informations.
        </p>

        <div class="rounded-lg bg-white text-gray-900 p-4">
            <form method="POST" id="edit-podcast-form" action="{{ route('channel.update', $channel) }}">
                @method('PATCH')
                @csrf

                <div class="pb-4">
                    <label class="block py-1" for="podcast_title">Podcast name</label>
                    <input type="text" id="podcast_title" name="podcast_title"
                        value="{{ old('podcast_title') ?? $channel->title() }}"
                        placeholder="Do your podcast or do not. There is no try." aria-label="Podcast name"
                        class="w-full px-5 py-1 text-gray-900 bg-gray-200 rounded placeholder-gray-700">
                </div>

                <div class="pb-4">
                    <label class="block py-1" for="authors">Author(s)</label>
                    <input type="text" id="authors" name="authors" value="{{ old('authors') ?? $channel->authors }}"
                        placeholder="Master yoda" aria-label="Podcast authors"
                        class="w-full px-5 py-1 text-gray-900 bg-gray-200 rounded placeholder-gray-700">
                </div>

                <div class="pb-4">
                    <label class="block py-1" for="authors">Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email') ?? $channel->email }}"
                        placeholder="yoda@usetheforce.com" aria-label="Podcast authors email"
                        class="w-full px-5 py-1 text-gray-900 bg-gray-200 rounded placeholder-gray-700">
                </div>

                <div class="pb-4">
                    <label class="block py-1" for="description">Description</label>
                    <textarea id="description" name="description"
                        class="w-full px-5 py-1 text-gray-900 bg-gray-200 rounded placeholder-gray-700">{{ old('description') ?? $channel->description }}</textarea>
                </div>

                <div class="pb-4">
                    <label class="block py-1" for="link">Website</label>
                    <input type="url" id="link" name="link" value="{{ old('link') ?? $channel->link }}"
                        placeholder="https://usetheforce.com" aria-label="Podcast website"
                        class="w-full px-5 py-1 text-gray-900 bg-gray-200 rounded placeholder-gray-700">
                </div>

                <div class="pb-4">
                    <label class="block py-1" for="category">Category</label>
                    <div class="relative inline-flex">
                        <svg class="w-2 h-2 absolute top-0 right-0 m-4 pointer-events-none"
                            xmlns="http://www.w3.org/2000/svg" viewBox="0 0 412 232">
                            <path
                                d="M206 171.144L42.678 7.822c-9.763-9.763-25.592-9.763-35.355 0-9.763 9.764-9.763 25.592 0 35.355l181 181c4.88 4.882 11.279 7.323 17.677 7.323s12.796-2.441 17.678-7.322l181-181c9.763-9.764 9.763-25.592 0-35.355-9.763-9.763-25.592-9.763-35.355 0L206 171.144z"
                                fill="#648299" fill-rule="nonzero" />
                        </svg>
                        <select id="category_id" name="category_id"
                            class="border border-gray-300 rounded-lg text-gray-900 h-10 pl-5 pr-10 bg-gray-200 hover:border-gray-400 focus:outline-none appearance-none">
                            @include('partials.categories', ['channelSelectedCategory' => $channel->category_id])
                        </select>
                    </div>
                </div>

                <div class="pb-4">
                    <label class="block py-1" for="lang">Language</label>
                    <div class="relative inline-flex">
                        <svg class="w-2 h-2 absolute top-0 right-0 m-4 pointer-events-none"
                            xmlns="http://www.w3.org/2000/svg" viewBox="0 0 412 232">
                            <path
                                d="M206 171.144L42.678 7.822c-9.763-9.763-25.592-9.763-35.355 0-9.763 9.764-9.763 25.592 0 35.355l181 181c4.88 4.882 11.279 7.323 17.677 7.323s12.796-2.441 17.678-7.322l181-181c9.763-9.764 9.763-25.592 0-35.355-9.763-9.763-25.592-9.763-35.355 0L206 171.144z"
                                fill="#648299" fill-rule="nonzero" />
                        </svg>
                        <select id="language_id" name="language_id"
                            class="border border-gray-300 rounded-lg text-gray-900 h-10 pl-5 pr-10 bg-gray-200 hover:border-gray-400 focus:outline-none appearance-none">
                            <option value="">Your podcast language</option>
                            @foreach ($languages as $language)
                                <option value="{{ $language->id }}"
                                    {{ $channel->language_id == $language->id ? ' selected' : '' }}>
                                    {{ $language->native_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="md:flex md:items-center pb-4">
                    <div class="md:w-1/3"></div>
                    <label for="explicit" class="md:w-2/3 block text-white font-bold">
                        <input class="mr-2 leading-tight" type="checkbox" id="explicit" name="explicit" value="1"
                            {{ $channel->explicit == 1 ? 'checked' : '' }}>
                        <span class="text-sm text-gray-800">Check it if your podcast contains explicit content.</span>
                    </label>
                </div>

                <div class="w-3/4 mx-auto pb-6 px-6 rounded-lg border-2 border-red-700 bg-red-200 text-center ">
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
                </div>
            </form>
        </div>


        <div class="mt-4 p-4">
            <form method="POST" id="delete-podcast-form" action="{{ route('channel.destroy', $channel) }}">
                @method('DELETE')
                @csrf
                <div class="w-3/4 mx-auto pb-6 px-6 rounded-lg border-8 border-red-700 bg-white text-center ">
                    <div class="p-4 font-semibold border-2 border-red-700 rounded bg-red-200 mt-4">
                        <strong>💀 DEAD zone ❌</strong><br>
                    </div>

                    <div class="p-4 text-red-800">
                        So you want to remove your podcast ?<br>
                        <span class="underline">Once clicked</span> on the button below, all of your podcast (and playlist
                        related) will <span class="underline">be permanently removed</span>.<br>
                        <strong>Do. or do not. There is no try</strong>.<br>
                        This action cannot be undone !
                    </div>

                    <div class="flex mt-4 justify-center items-center">
                        <a href="#"
                            onclick="event.preventDefault(); document.getElementById('delete-podcast-form').submit();">
                            <button type="submit"
                                class="flex-1 bg-red-800 text-gray-100 hover:bg-red-700 font-bold py-2 px-4 rounded-lg">Delete
                                my podcast !</button>
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

@endsection
