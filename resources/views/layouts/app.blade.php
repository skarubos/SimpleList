<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.1.0/css/all.css" integrity="sha384-lKuwvrZot6UHsBSfcMvOkWwlCMgc0TaWr+30HWe3a4ltaBwTZhyTEggF5tJv8tbt" crossorigin="anonymous">
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white dark:bg-gray-800 shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>

            @if ($errors->any())
                <div class="block bg-red-500 text-white p-10 py-2">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- 書き込み成功メッセージ -->
            @if (session('success'))
                <div class="block bg-green-500 text-white p-10 py-2">
                    {{ session('success') }}
                </div>
            @endif

            {{ $slot }}
            @yield('content')
            
            </main>
        </div>
    </body>

    <script>
        // document.addEventListener('DOMContentLoaded', function () {
        //     setTimeout(function () {
        //         var flashMessage = document.getElementById('flash-message');
        //         if (flashMessage) {
        //             flashMessage.style.transition = 'opacity 1s ease';
        //             flashMessage.style.opacity = '0';
                    
        //             // 完全に非表示にするために少し待つ
        //             setTimeout(function() {
        //                 flashMessage.style.display = 'none';
        //             }, 1000);
        //         }
        //     }, 3000); // 3秒後にメッセージを隠す
        // });
    </script>


</html>
