<header x-data="{ open: false }" class="text-gray-100 sm:flex sm:justify-between sm:py-2 sm:px-4 max-w-screen-xl mx-auto">
    <div class="flex items-center justify-between py-2 sm:p-0">
        <div>
            @guest
                <a href="{{ route('www.index') }}">
                    <img class="h-12" src="/images/podmytube-logo-2020-150x53.png">
                </a>
            @else
                <a href="{{ route('home') }}">
                    <img class="h-12" src="/images/podmytube-logo-2020-150x53.png">
                </a>
            @endguest
        </div>
        <div class="pr-2 sm:hidden">
            <button id="mobile-nav-trigger" x-on:click="open = ! open"
                class="text-gray-400 hover:text-white focus:text-white focus:outline-none">
                <svg class="fill-current h-5 w-5" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                    <title>Menu</title>
                    <path d="M0 3h20v2H0V3zm0 6h20v2H0V9zm0 6h20v2H0v-2z"></path>
                </svg>
            </button>
        </div>
    </div>

    {{-- mobile display for nav --}}
    <div x-show="open" class="text-gray-900 block sm:hidden">
        @include ('partials.navbar-links')
    </div>

    {{-- navbar for sm => large screen display for nav --}}
    <div class="hidden sm:block">
        @include ('partials.navbar-links')
    </div>
</header>

<form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
    @csrf
</form>

<script>
    document.addEventListener("DOMContentLoaded", function(event) {
        var logoutLink = document.getElementById("logout-link");
        if (logoutLink) {
            logoutLink.addEventListener("click", function(event) {
                document.getElementById('logout-form').submit();
            });
        }
    });
</script>
