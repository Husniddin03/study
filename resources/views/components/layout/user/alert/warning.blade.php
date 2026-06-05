<div class="fixed top-5 right-5 z-50 space-y-4 w-full max-w-md">

    @if(session('success'))
        <div
            x-data="{ show: true }"
            x-init="setTimeout(() => show = false, 5000)"
            x-show="show"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-x-10"
            x-transition:enter-end="opacity-100 translate-x-0"
            class="relative overflow-hidden rounded-lg border border-green-500 bg-black/95 shadow-[0_0_20px_rgba(34,197,94,0.4)]"
        >
            <div class="absolute top-0 left-0 h-full w-1 bg-green-500"></div>

            <div class="p-4 font-mono">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="text-green-400">$</span>
                        <span class="text-green-400 font-bold">
                            SYSTEM SUCCESS
                        </span>
                    </div>

                    <button
                        @click="show = false"
                        class="text-green-500 hover:text-green-300"
                    >
                        ✕
                    </button>
                </div>

                <div class="mt-3 text-sm text-green-300">
                    > {{ session('success') }}
                </div>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div
            x-data="{ show: true }"
            x-init="setTimeout(() => show = false, 5000)"
            x-show="show"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-x-10"
            x-transition:enter-end="opacity-100 translate-x-0"
            class="relative overflow-hidden rounded-lg border border-red-500 bg-black/95 shadow-[0_0_20px_rgba(239,68,68,0.4)]"
        >
            <div class="absolute top-0 left-0 h-full w-1 bg-red-500"></div>

            <div class="p-4 font-mono">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="text-red-400">!</span>
                        <span class="text-red-400 font-bold">
                            SYSTEM ERROR
                        </span>
                    </div>

                    <button
                        @click="show = false"
                        class="text-red-500 hover:text-red-300"
                    >
                        ✕
                    </button>
                </div>

                <div class="mt-3 text-sm text-red-300">
                    > {{ session('error') }}
                </div>
            </div>
        </div>
    @endif

</div>