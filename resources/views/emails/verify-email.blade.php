<x-layout.user.app>
    <x-slot:title>Verify Email</x-slot:title>

    <a href="{{ $verificationUrl }}" class="cursor-pointer hover:border-[#ff5c00] transition-colors duration-300 border border-gray-300 dark:border-gray-600 w-40 h-40 rounded-md flex flex-col justify-center items-center">
        Akkountni tasdiqlash
    </a>

    <p class="text-red-500 text-xs">Eslatma: Ushbu havola xavfsizlik yuzasidan faqat 24 soat davomida faol
        bo'ladi.</p>
</x-layout.user.app>
