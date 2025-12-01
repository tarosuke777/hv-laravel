<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>動画一覧 V2</title>
</head>
<body>
    <h1>🎥 動画一覧 V2</h1>

    {{-- route('videos.index') の名前付きルートは変更していません --}}
    <form action="{{ route('videos.index') }}" method="GET">
        <input type="text" 
               name="search" 
               placeholder="タイトルや投稿者名で検索..." 
               value="{{ $search }}">
        <button type="submit">検索</button>
        @if($search)
            <a href="{{ route('videos.index') }}">リセット</a>
        @endif
    </form>

    <hr>

    @if($videos->isEmpty())
        <p>該当する動画が見つかりませんでした。</p>
    @else
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>タイトル</th>
                    <th>投稿者名</th>
                    <th>ファイル名</th>
                    <th>投稿日時</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($videos as $video)
                <tr>
                    <td>{{ $video->id }}</td>
                    <td>{{ $video->titile }}</td>
                    <td>{{ $video->name }}</td>
                    <td>{{ $video->file_name }}</td>
                    <td>{{ $video->created_at->format('Y/m/d H:i') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        {{ $videos->appends(['search' => $search])->links() }}

    @endif
</body>
</html>