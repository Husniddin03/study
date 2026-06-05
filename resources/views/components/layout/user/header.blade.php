<header class="sticky top-0 z-50 backdrop-blur-md flex py-2 container mx-auto">
    <nav class="w-full">
        <ul class="flex gap-6 py-4 justify-start items-center">
            <li>
                <a href="{{ route('home') }}" class="text-lg font-bold hover:text-[#ff5c00]">Home</a>
            </li>
            <li>
                <a href="{{ route('about') }}" class="text-lg font-bold hover:text-[#ff5c00]">About</a>
            </li>
        </ul>
    </nav>
    <div class="flex gap-6 py-4 justify-end items-center">
        @auth
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="text-lg font-bold hover:text-[#ff5c00]">Logout</button>
            </form>
        @else
            <a href="{{ route('login') }}" class="text-lg font-bold hover:text-[#ff5c00]">Login</a>
            <a href="{{ route('register') }}" class="text-lg font-bold hover:text-[#ff5c00]">Register</a>
        @endauth
    </div>
</header>
