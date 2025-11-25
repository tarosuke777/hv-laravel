{{-- resources/views/videos/index.blade.php --}}

{{-- ★ 1. layouts/app.blade.php を継承する ★ --}}
@extends('layouts.app') 

{{-- ★ 2. ページタイトルを定義する (app.blade.phpの@yield('title')に挿入される) ★ --}}
@section('title', 'MP4 動画一覧')

{{-- ★ 3. メインコンテンツを定義する (app.blade.phpの@yield('content')に挿入される) ★ --}}
@section('content')

    <h1 class="text-3xl font-bold text-gray-900 mb-6">🎬 {{ $directory }} ディレクトリの MP4 ファイル一覧</h1>
    
    @if (count($videoList) > 0)
        <div class="mb-8">
            {{ $videos->links() }}
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
            {{ $videos->links() }}
        </div>
    @else
        <p class="text-gray-500">MP4 ファイルが見つかりません。</p>
    @endif

@endsection