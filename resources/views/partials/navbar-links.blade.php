<div class="pt-2 pb-4 text-center sm:flex sm:items-center sm:p-0">
    @guest
        <a class="block px-4 py-2 text-gray-100 rounded hover:bg-gray-600" href="{{ route('pricing') }}">
            Pricing</a>

        <a class="mt-1 block px-4 py-2 text-gray-100 rounded hover:bg-gray-600 sm:mt-0 sm:mr-2"
            href="{{ route('post.index') }}">
            Blog</a>

        <a class="mt-1 block px-4 py-2 text-gray-100 rounded hover:bg-gray-600 sm:mt-0 sm:mr-2" href="{{ route('about') }}">
            About</a>

        <a class="mt-1 block px-4 py-2 text-gray-100 rounded hover:bg-gray-600 sm:mt-0 sm:mr-2" href="{{ route('faq') }}">
            FAQ</a>

        <a class="mt-1 block px-4 py-2 text-gray-100 rounded hover:bg-gray-600 sm:mt-0 sm:mr-2" href="{{ route('login') }}">
            Log In</a>

        <a class="mt-1 block px-4 py-2 text-gray-100 rounded hover:bg-gray-600 sm:mt-0 sm:mr-2 sm:hover:bg-white sm:border sm:rounded sm:border-white hover:border-transparent hover:text-gray-900"
            href="{{ route('register') }}">
            Sign Up </a>
    @else
        <a class="block px-4 py-2 text-gray-100 rounded hover:bg-gray-600" href="{{ route('home') }}">
            Dashboard</a>

        <a class="mt-1 block px-4 py-2 text-gray-100 rounded hover:bg-gray-600 sm:mt-0 sm:mr-2"
            href="{{ route('user.index') }}">
            Profile</a>

        <a class="mt-1 block px-4 py-2 text-gray-100 rounded hover:bg-gray-600 sm:mt-0 sm:mr-2"
            href="{{ route('channel.step1') }}">
            Add your podcast</a>

        <form id="logout-form" action="{{ route('logout') }}" method="POST">
            @csrf
            <button class="mt-1 block px-4 py-2 text-gray-100 rounded hover:bg-gray-600 sm:mt-0 sm:mr-2" type="submit">
                Logout
            </button>
        </form>
        @if (session('impersonated_by'))
            <a class="mt-1 block px-4 py-2 text-gray-100 rounded hover:bg-gray-600 sm:mt-0 sm:mr-2"
                href="{{ route('users.leave-impersonate') }}">Leave Impersonation</a>
        @endif
    @endguest
</div>
