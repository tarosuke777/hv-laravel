@extends('layouts.viewer')

@section('content')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />

    {{-- Swiper 本体 --}}
    <div class="swiper" dir="rtl">
        <div class="swiper-wrapper">
            @foreach($pages as $page)
                <div class="swiper-slide">
                    <img src="{{ $page->external_url }}" loading="lazy">
                </div>
            @endforeach

            {{-- ページ数が奇数の場合、空のスライドを追加して調整する --}}
            @if(count($pages) % 2 !== 0)
                <div class="swiper-slide empty-slide" style="background: #000;"></div>
            @endif
        </div>
        <!-- 必要に応じてナビゲーションやページネーションを追加 -->
        <div class="swiper-pagination"></div>
        <div class="swiper-button-prev"></div>
        <div class="swiper-button-next"></div>
    </div>

<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script>
    const swiper = new Swiper(".swiper", {
        rtl: true,
        loop: false,
        breakpoints: {
            spaceBetween: 0, // スライド間の余白を0にする
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
        pagination: {
            el: ".swiper-pagination",
            type: "fraction", // "1 / 10" 形式
        },
        navigation: {
            nextEl: ".swiper-button-next",
            prevEl: ".swiper-button-prev",
        },
        keyboard: {
            enabled: true,
        }
    });
</script>

<style>
    .swiper {
        width: 100%;
        height: 100vh;
    }
    .swiper-slide {
        display: flex;
        background: #000;
        align-items: center;
    }

    .swiper-slide img {
        /* ここがポイント：縦横どちらかが先に限界に達するようにする */
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
    }

    /* 奇数番目のスライド（左側）：中身を右に寄せる */
    .swiper-slide:nth-child(odd) {
        justify-content: flex-end;
    }
    
    /* 偶数番目のスライド（右側）：中身を左に寄せる */
    .swiper-slide:nth-child(even) {
        justify-content: flex-start;
    }

    /* スマホなどで1枚表示（slidesPerView: 1）になる時のためのリセット */
    @media (max-width: 1023px) {
        .swiper-slide {
            justify-content: center !important;
        }
    }
</style>
@endsection
