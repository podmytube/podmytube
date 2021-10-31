<header class="text-gray-100 sm:flex sm:justify-between sm:py-2 sm:px-4 max-w-screen-xl mx-auto">
    <div class="flex items-center justify-between py-2 sm:p-0">
        <div>
            @guest
                <a href="{{ route('www.index') }}">
                @else
                    <a href="{{ route('home') }}">
                    @endguest
                    <img class="h-12" src="/images/podmytube-logo-2020-150x53.png">
                </a>
        </div>
    </div>
</header>
