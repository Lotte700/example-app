<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    

    <!-- Fonts -->
  

    <!-- Bootstrap Icons (ใช้ CDN ได้ ไม่กระทบ) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&family=Kanit:wght@300;400;500&display=swap" rel="stylesheet">

    

    <!-- ✅ Vite only -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
</head>
<body>
    <div class="min-h-screen bg-gray-100">
        @include('layouts.navigation')

        <main>
            <div class="container mt-4">
                @yield('content')
            </div>
        </main>
    </div>
</body>
</html>
