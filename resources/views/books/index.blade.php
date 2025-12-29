@extends('layouts.app')

@section('title', '漫画一覧')

@section('content')
    <h1 class="text-3xl font-bold text-gray-900 mb-6">📚 漫画ライブラリ</h1>

    <div x-data="{ open: false }" class="mb-8 border p-4 bg-gray-50 rounded-lg">
        <div class="flex items-center justify-between cursor-pointer" @click="open = !open">
            <h2 class="text-xl font-semibold">
                タイトルで絞り込む
                @if ($selectedTitle)
                    <span class="text-sm font-normal text-blue-600 ml-2"> (現在: {{ $selectedTitle }})</span>
                @endif
            </h2>
            
            {{-- 展開アイコン（openの状態に応じて回転） --}}
            <svg class="w-5 h-5 transition-transform duration-300" 
                 :class="{ 'rotate-180': open, 'rotate-0': !open }" 
                 fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
        </div>

        <div x-show="open" x-collapse.duration.300ms class="mt-4 pt-4 border-t border-gray-300">
            <div class="flex flex-wrap gap-2">

                {{-- 1. 全て表示リンク --}}
                <a href="{{ route('videos.indexV2') }}" 
                class="px-3 py-1 text-sm rounded-full transition duration-150 
                         {{ $selectedTitle ? 'bg-gray-200 text-gray-700 hover:bg-gray-300' : 'bg-blue-600 text-white font-bold' }}">
                    全ての動画 ({{ count($uniqueTitles) }})
                </a>

                {{-- 2. 重複のないタイトルごとのリンク --}}
                @foreach ($uniqueTitles as $title)
                    {{-- リンクURL: /videos?title=【URLエンコードされたタイトル】 --}}
                    <a href="{{ route('videos.indexV2', ['title' => $title]) }}"
                    class="px-3 py-1 text-sm rounded-full transition duration-150 
                             {{ $selectedTitle === $title ? 'bg-blue-600 text-white font-bold' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                        {{ $title }}
                    </a>
                @endforeach
            </div>
        </div>
    </div>

    @if ($books->count() > 0)
        <div class="mt-8">{{ $books->links() }}</div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach ($books as $book)
                <div class="bg-white shadow-lg rounded-xl overflow-hidden flex flex-col border border-gray-200 hover:shadow-2xl transition duration-300">
                    
                    {{-- 表紙画像 (最初の1枚を表示) --}}
                    <div class="aspect-[3/4] bg-gray-200 relative">
                        @if($book->pages->first())
                            <img src="{{ $book->pages->first()->external_url }}" 
                                 alt="cover" class="w-full h-full object-cover">
                        @else
                            <div class="flex items-center justify-center h-full text-gray-400">No Image</div>
                        @endif
                        
                        {{-- ページ数バッジ --}}
                        <span class="absolute bottom-2 right-2 bg-black/60 text-white text-xs px-2 py-1 rounded">
                            {{ $book->pages->count() }}P
                        </span>
                    </div>

                    <div class="p-4 flex-1 flex flex-col">
                        <h2 class="text-lg font-bold text-gray-800 line-clamp-2 mb-1">{{ $book->title }}</h2>
                        <p class="text-sm text-gray-500 mb-4">{{ $book->author ?? '不明' }}</p>

                        {{-- 閲覧ボタン --}}
                        <a href="{{ route('books.show', ['book' => $book->id, 'external' => 'true']) }}" 
                           target="_blank" 
                           class="mt-auto w-full bg-blue-600 text-white text-center py-2 rounded-lg font-bold hover:bg-blue-700 transition">
                            読む (別タブ)
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
@endsection

