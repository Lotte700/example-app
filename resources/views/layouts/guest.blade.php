<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

       

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
   <body class="font-sans text-gray-900 antialiased">
    <div class="min-h-screen flex flex-col justify-center items-center bg-gray-100 p-6">
        
        <div class="w-full sm:max-w-md bg-white shadow-2xl rounded-3xl overflow-hidden border border-gray-100">
            <div class="p-8 sm:p-12">
                {{ $slot }}
            </div>
        </div>

    </div>
</body>
</html>