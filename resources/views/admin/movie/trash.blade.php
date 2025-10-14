@extends('templates.app')

@section('content')
    <div class="container mt-5">
        @if (Session::get('success'))
            <div class="alert alert-success w-100 mt-3">{{ Session::get('success') }}</div>
        @endif
        <div class="d-flex justify-content-end">
            <a href="{{ route('admin.movies.index') }}" class="btn btn-secondary">Kembali</a>
        </div>
        <h3 class="my-3">Data Sampah Film</h3>
        <table class="table table-bordered">
            <tr>
                <th>No</th>
                <th>Poster</th>
                <th>Judul</th>
                <th>Genre</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
            @foreach ($movieTrash as $key => $movie)
                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td class="text-center">
                        @if ($movie->poster)
                            <img src="{{ asset('storage/' . $movie->poster) }}" alt="{{ $movie->title }}"
                                class="img-thumbnail" style="max-width: 100px;">
                        @else
                            <span class="text-muted">No image</span>
                        @endif
                    </td>
                    <td>{{ $movie->title }}</td>
                    <td>{{ $movie->genre }}</td>
                    <td class="text-center">
                        @if ($movie->actived == 1)
                            <span class="badge bg-success">Aktif</span>
                        @else
                            <span class="badge bg-secondary">Non-aktif</span>
                        @endif
                    </td>
                    <td class="d-flex align-items-center justify-content-center">
                        <form action="{{ route('admin.movies.restore', $movie->id) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button class="btn btn-success mx-2">Kembalikan</button>
                        </form>
                        <form action="{{ route('admin.movies.delete_permanent', $movie->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger mx-2" onclick="return confirm('Yakin hapus permanen?')">
                                Hapus Permanen
                            </button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </table>
    </div>
@endsection
