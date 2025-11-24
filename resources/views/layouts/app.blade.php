<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <title>@yield('title', config('app.name', 'Laravel App'))</title>
        
    {{-- resources/css/app.css で Tailwind CSS を読み込んでいます --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased bg-gray-100">
    
    {{-- ----------------------------------------------------------------- --}}
    {{-- 1. ヘッダー / ナビゲーションバー (全ページ共通) --}}
    {{-- ----------------------------------------------------------------- --}}
    <header class="bg-white shadow-md">
        <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3 flex justify-between items-center">
            <a href="/" class="text-xl font-bold text-gray-800">{{ config('app.name', 'Laravel') }}</a>
            {{-- ここにナビゲーションリンクを配置します --}}
            <div class="space-x-4">
                <a href="/videos" class="text-gray-600 hover:text-gray-900">動画一覧</a>
                {{-- @auth / @guest など認証状態によるリンク切り替え --}}
            </div>
        </nav>
    </header>

    {{-- ----------------------------------------------------------------- --}}
    {{-- 2. メインコンテンツ領域 (子テンプレートが埋める場所) --}}
    {{-- ----------------------------------------------------------------- --}}
    <main class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- ★ 子テンプレートのコンテンツがここに挿入されます ★ --}}
            @yield('content')
        </div>
    </main>

    {{-- ----------------------------------------------------------------- --}}
    {{-- 3. フッター (全ページ共通) --}}
    {{-- ----------------------------------------------------------------- --}}
    <footer class="mt-10 py-4 text-center text-sm text-gray-500 border-t">
        &copy; {{ date('Y') }} {{ config('app.name', 'Laravel') }}. All rights reserved.
    </footer>

</body>
</html>