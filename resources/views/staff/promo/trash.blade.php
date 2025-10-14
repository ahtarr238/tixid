@extends('templates.app')

@section('content')
    <div class="container mt-5">
        @if (Session::get('success'))
            <div class="alert alert-success w-100 mt-3">{{ Session::get('success') }}</div>
        @endif
        <div class="d-flex justify-content-end">
            <a href="{{ route('staff.promos.index') }}" class="btn btn-secondary">Kembali</a>
        </div>
        <h3 class="my-3">Data Sampah Promo</h3>
        <table class="table table-bordered">
            <tr>
                <th>No</th>
                <th>Kode Promo</th>
                <th>Potongan Harga</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
            @foreach ($promoTrash as $key => $promo)
                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>{{ $promo->promo_code }}</td>
                    <td>{{ $promo->type == 'rupiah' ? 'Rp ' : '' }}{{ number_format($promo->discount, 0, ',', '.') }}{{ $promo->type == 'percent' ? '%' : '' }}</td>
                    <td>
                        @if($promo->actived == 1)
                            <span class="badge bg-success">Aktif</span>
                        @else
                            <span class="badge bg-secondary">Non-aktif</span>
                        @endif
                    </td>
                    <td class="d-flex align-items-center">
                        <form action="{{ route('staff.promos.restore', $promo->id) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button class="btn btn-danger ms-2">Kembalikan</button>
                        </form>
                        <form action="{{ route('staff.promos.delete_permanent', $promo->id) }}" method="POST" class="ms-2">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger ms-2">Hapus Permanen</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </table>
    </div>
@endsection