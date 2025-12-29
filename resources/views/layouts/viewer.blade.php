<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', config('app.name', 'Laravel App'))</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* レイアウト崩れを防ぐための必須設定 */
        html, body {
            margin: 0;
            padding: 0;
            height: 100vh;
            width: 100vw;
            overflow: hidden;
            background-color: #000;
        }
    </style>
</head>
<body class="bg-black"> {{-- 背景を黒にする --}}
    @yield('content')
</body>
</html>