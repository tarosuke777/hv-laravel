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
    
    {{-- 1. ヘッダー / ナビゲーションバー --}}
    <header class="bg-white shadow-md" x-data="{ open: false }">
        <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3">
            <div class="flex justify-between items-center h-12">
                <a href="/" class="text-xl font-bold text-gray-800">{{ config('app.name', 'HV') }}</a>

                @php
                    // メニュー項目を配列で定義
                    $navItems = [
                        ['name' => '動画一覧', 'url' => '/videos'],
                        ['name' => '画像一覧', 'url' => '/images'],
                        ['name' => '書籍一覧', 'url' => '/books'],
                    ];
                @endphp

                <div class="hidden sm:flex space-x-8">
                    @foreach($navItems as $item)
                        <a href="{{ $item['url'] }}" class="text-gray-600 hover:text-gray-900">
                            {{ $item['name'] }}
                        </a>
                    @endforeach
                </div>

                <div class="flex items-center sm:hidden">
                    <button @click="open = !open" class="text-gray-600 hover:text-gray-900 focus:outline-none">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path :class="{'hidden': open, 'inline-flex': !open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            <path :class="{'hidden': !open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>

            <div :class="{'block': open, 'hidden': !open}" class="hidden sm:hidden pb-4">
                @foreach($navItems as $item)
                    <a href="{{ $item['url'] }}" class="block py-2 text-gray-600 hover:text-gray-900">
                        {{ $item['name'] }}
                    </a>
                @endforeach
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