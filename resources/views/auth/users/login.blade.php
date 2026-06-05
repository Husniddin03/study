<x-layout.user.app>
    <x-slot:title>Login</x-slot:title>
    <section class="w-full h-full flex justify-center items-center">
        <div class="w-1/3 h-1/2">
            <h2 class="text-4xl font-bold py-10">Login</h2>
            <form action="{{ route('login.store') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
                    <input type="email" name="email" id="email" class="mt-1 p-2 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600">
                </div>
                <div class="mb-4">
                    <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Password</label>
                    <input type="password" name="password" id="password" class="mt-1 p-2 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600">
                </div>
                <div class="flex items-center justify-between">
                    <p>Don't have an account? <a href="{{ route('register') }}" class="text-indigo-500 underline underline-offset-2">Register</a></p>
                    <button type="submit" class="px-4 py-2 bg-indigo-500 text-white rounded-md">Login</button>
                </div>
            </form>
        </div>
    </section>
</x-layout.user.app>