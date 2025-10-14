@extends('templates.app')

@section('content')
    <div class="container mt-5">
        @if (Session::get('success'))
            <div class="alert alert-success w-100 mt-3">{{ Session::get('success') }}</div>
        @endif
        @if (Session::get('error'))
            <div class="alert alert-danger w-100 mt-3">{{ Session::get('error') }}</div>
        @endif

        <div class="d-flex justify-content-between mb-3">
            <h3>Data Bioskop (Recycle Bin)</h3>
            <div>
                <a href="{{ route('admin.cinemas.index') }}" class="btn btn-secondary">Kembali</a>
            </div>
        </div>

        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Data Bioskop yang Dihapus</h5>
            </div>
            <div class="card-body">
                @if($cinemas->count() > 0)
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Bioskop</th>
                                <th>Alamat</th>
                                <th>Tanggal Dihapus</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($cinemas as $key => $cinema)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ $cinema->name }}</td>
                                    <td>{{ $cinema->location }}</td>
                                    <td>{{ \Carbon\Carbon::parse($cinema->deleted_at)->format('d M Y H:i') }}</td>
                                    <td>
                                        <div class="d-flex">
                                            <form action="{{ route('admin.cinemas.restore', $cinema->id) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-warning btn-sm me-2">
                                                    <i class="fas fa-undo"></i> Kembalikan
                                                </button>
                                            </form>
                                            <form action="{{ route('admin.cinemas.delete_permanent', $cinema->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini secara permanen?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm">
                                                    <i class="fas fa-trash-alt"></i> Hapus Permanen
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center mt-3">
                        {{ $cinemas->links() }}
                    </div>

                    <div class="mt-3">
                        <div class="d-flex justify-content-between">
                            <form action="{{ route('admin.cinemas.restore_all') }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-warning" onclick="return confirm('Apakah Anda yakin ingin mengembalikan semua data?')">
                                    <i class="fas fa-undo"></i> Kembalikan Semua
                                </button>
                            </form>
                            <form action="{{ route('admin.cinemas.delete_all_permanent') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus semua data secara permanen?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">
                                    <i class="fas fa-trash-alt"></i> Hapus Semua Permanen
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <div class="alert alert-info text-center">
                        <i class="fas fa-info-circle"></i> Tidak ada data di recycle bin
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
