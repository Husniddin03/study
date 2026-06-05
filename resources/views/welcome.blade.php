<x-layout.user.app>
    <x-slot:title>Home</x-slot:title>
    <section>
        <div class="flex flex-col justify-center items-center">
            <h2 class="text-4xl font-bold py-10">Categories</h2>
            <div class="grid grid-cols-7 gap-4">
                @for ($i = 0; $i < 28; $i++)
                    <button
                        class="cursor-pointer hover:border-[#ff5c00] transition-colors duration-300 border border-gray-300 dark:border-gray-600 w-40 h-40 rounded-md flex flex-col justify-center items-center">
                        <p class="text-lg">Category {{ $i }}</p>
                    </button>
                @endfor
            </div>
        </div>
    </section>
</x-layout.user.app>