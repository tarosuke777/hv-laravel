            @foreach ($books as $book)
                <div
                    class="bg-white shadow-lg rounded-xl overflow-hidden flex flex-col border border-gray-200 hover:shadow-2xl transition duration-300">

                    <div class="p-4 flex-1 flex flex-col">
                        <h2 class="text-lg font-bold text-gray-800 line-clamp-2 mb-1">{{ $book->title }}</h2>
                        <p class="text-sm text-gray-500 mb-4">{{ $book->author ?? '不明' }}</p>
                    </div>

                    {{-- 表紙画像 (最初の1枚を表示) --}}
                    <a href="{{ route('books.show', ['book' => $book->id, 'external' => 'true']) }}" target="_blank" class="block aspect-[3/4] bg-gray-200 relative group">
                            @if ($book->pages->first())
                            <img src="{{ $book->pages->first()->external_url }}" alt="cover"
                                class="w-full h-full object-cover">
                        @else
                            <div class="flex items-center justify-center h-full text-gray-400">No Image</div>
                        @endif

                        {{-- ページ数バッジ --}}
                        <span class="absolute bottom-2 right-2 bg-black/60 text-white text-xs px-2 py-1 rounded">
                            {{ $book->pages->count() }}P
                        </span>
                    </a>
                </div>
            @endforeach
