{{-- resources/views/videos/index.blade.php --}}

{{-- ★ 1. layouts/app.blade.php を継承する ★ --}}
@extends('layouts.app') 

{{-- ★ 2. ページタイトルを定義する (app.blade.phpの@yield('title')に挿入される) ★ --}}
@section('title', 'MP4 動画一覧')

{{-- ★ 3. メインコンテンツを定義する (app.blade.phpの@yield('content')に挿入される) ★ --}}
@section('content')

    <h1 class="text-3xl font-bold text-gray-900 mb-6">🎬 {{ $directory }} ディレクトリの MP4 ファイル一覧</h1>
    
    <div class="mb-8 border p-4 bg-gray-50 rounded-lg">
        <h2 class="text-xl font-semibold mb-3">タイトルで絞り込む</h2>
        <div class="flex flex-wrap gap-2">

            {{-- 1. 全て表示リンク --}}
            <a href="{{ route('videos.index') }}" 
               class="px-3 py-1 text-sm rounded-full transition duration-150 
                      {{ $selectedTitle ? 'bg-gray-200 text-gray-700 hover:bg-gray-300' : 'bg-blue-600 text-white font-bold' }}">
                全ての動画 ({{ count($uniqueTitles) }})
            </a>

            {{-- 2. 重複のないタイトルごとのリンク --}}
            @foreach ($uniqueTitles as $title)
                {{-- リンクURL: /videos?title=【URLエンコードされたタイトル】 --}}
                <a href="{{ route('videos.index', ['title' => $title]) }}"
                   class="px-3 py-1 text-sm rounded-full transition duration-150 
                          {{ $selectedTitle === $title ? 'bg-blue-600 text-white font-bold' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                    {{ $title }}
                </a>
            @endforeach
        </div>
    </div>

    @if (count($videoList) > 0)
        <div class="mb-8">
            {{ $videos->appends(request()->query())->links() }}
        </div>

        {{-- Tailwind CSS のクラスで横3列のグリッドレイアウトを定義 --}}
        <ul class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 list-none p-0 m-0">
            @foreach ($videoList as $video)
                <li class="bg-white shadow-xl rounded-xl overflow-hidden p-5 flex flex-col items-center">
                    
                    {{-- 動画タイトル --}}
                    <strong class="text-lg font-semibold mb-3">{{ $video['name'] }}</strong>
                    
                    {{-- 動画プレーヤー --}}
                    {{-- 動画幅をカードに合わせ、シークバー問題解決後のクラスを適用 --}}
                    <video controls class="w-full h-auto rounded-lg mb-4">
                        <source src="{{ $video['url'] }}" type="video/mp4">
                        お使いのブラウザは動画タグに対応していません。
                    </video>
                    
                    {{-- 再生リンク --}}
                    <a href="{{ $video['url'] }}" target="_blank" class="text-blue-600 hover:text-blue-800 font-medium transition duration-150">
                        別タブで再生
                    </a>
                </li>
            @endforeach
        </ul>

        <div class="mt-8">
            {{ $videos->appends(request()->query())->links() }}
        </div>
    @else
        <p class="text-gray-500">
            @if($selectedTitle)
                「{{ $selectedTitle }}」の動画は見つかりませんでした。
            @else
                MP4 ファイルが見つかりません。
            @endif
        </p>
    @endif

@endsection