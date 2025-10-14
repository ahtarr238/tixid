
@extends('templates.app')

@section('content')
    <div class="container mt-5">
        @if (Session::get('success'))
            <div class="alert alert-success w-100 mt-3">{{ Session::get('success') }}</div>
        @endif
        <div class="d-flex justify-content-end">
            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Kembali</a>
        </div>
        <h3 class="my-3">Data Sampah Pengguna</h3>
        <table class="table table-bordered">
            <tr>
                <th>No</th>
                <th>Nama</th>
                <th>Email</th>
                <th>Role</th>
                <th>Aksi</th>
            </tr>
            @foreach ($userTrash as $key => $user)
                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>
                        <span class="badge {{ $user->role == 'admin' ? 'bg-danger' : ($user->role == 'staff' ? 'bg-primary' : 'bg-secondary') }}">
                            {{ ucfirst($user->role) }}
                        </span>
                    </td>
                    <td class="d-flex align-items-center justify-content-center">
                        <form action="{{ route('admin.users.restore', $user->id) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button class="btn btn-success mx-2">Kembalikan</button>
                        </form>
                        <form action="{{ route('admin.users.delete_permanent', $user->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger mx-2">
                                Hapus Permanen
                            </button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </table>
    </div>
@endsection