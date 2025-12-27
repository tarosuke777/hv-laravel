@extends('layouts.app') {{-- または専用の真っ黒なレイアウト --}}

@section('content')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />

<div class="h-screen bg-black flex flex-col">
    {{-- ヘッダー：タイトルと閉じるボタン --}}
    <div class="p-4 bg-gray-900 text-white flex justify-between items-center">
        <h1 class="font-bold truncate">{{ $book->title }}</h1>
        <button onclick="window.close()" class="text-sm bg-gray-700 px-3 py-1 rounded">閉じる</button>
    </div>

    {{-- Swiper 本体 --}}
    <div class="swiper mySwiper flex-1 w-full">
        <div class="swiper-wrapper">
            @foreach($pages as $page)
                <div class="swiper-slide flex items-center justify-center">
                    <img src="{{ $page->external_url }}" 
                         class="max-h-full max-w-full object-contain"
                         loading="lazy">
                </div>
            @endforeach
        </div>
        {{-- ナビゲーションボタン --}}
        <div class="swiper-button-next text-white"></div>
        <div class="swiper-button-prev text-white"></div>
        {{-- ページ番号表示 --}}
        <div class="swiper-pagination text-white !bottom-4"></div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script>
    const swiper = new Swiper(".mySwiper", {
        loop: false,
        zoom: true, // ピンチズーム有効化
        breakpoints: {
            // 画面幅が1024px以上のとき（PCなど）は見開き
            1024: {
                slidesPerView: 2,
                slidesPerGroup: 2,
            },
            // それ以下のとき（スマホなど）は1枚表示
            0: {
                slidesPerView: 1,
                slidesPerGroup: 1,
            }
        },
        centeredSlides: false,  // 左（右）詰めに設定
        pagination: {
            el: ".swiper-pagination",
            type: "fraction", // "1 / 10" 形式
        },
        navigation: {
            nextEl: ".swiper-button-next",
            prevEl: ".swiper-button-prev",
        },
        // rtl: {{ $book->reading_direction === 'rtl' ? 'true' : 'false' }},
        keyboard: {
            enabled: true,
        }
    });
</script>

<style>
    body { margin: 0; padding: 0; overflow: hidden; }
    .swiper-slide { background: #000; }
</style>
@endsection