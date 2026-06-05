<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ $title ?? 'Study.com' }}</title>

    @fonts

    @vite(['resources/css/app.css', 'resources/js/app.js'])

</head>

<body class="bg-[#FDFDFC] dark:bg-[#0a0a0a] text-[#0000] dark:text-[#fff] min-h-screen flex flex-col">
    <x-layout.user.header />
    <main class="flex-1 container mx-auto my-8 w-full flex items-center justify-center">
        <x-layout.user.alert.warning />
        {{ $slot }}
    </main>
    <x-layout.user.footer />
</body>

</html>
