@extends('templates.app')

@section('content')
    <div class="container mt-5">
        <h5>Tambah Promo</h5>
        <form action="{{ route('staff.promos.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="promo_code" class="form-label">Kode Promo</label>
                <input type="text" class="form-control" id="promo_code" name="promo_code" required>
            </div>
            <div class="mb-3">
                <label for="discount" class="form-label">Diskon</label>
                <input type="number" class="form-control" id="discount" name="discount" min="1" required>
            </div>
            <div class="mb-3">
                <label for="type" class="form-label">Tipe</label>
                <select class="form-control" id="type" name="type" required>
                    <option value="percent">Persen</option>
                    <option value="rupiah">Rupiah</option>
                </select>
            </div>
            <button type="submit" class="btn btn-success">Simpan</button>
            <a href="{{ route('staff.promos.index') }}" class="btn btn-secondary mx-3">Kembali</a>
        </form>
    </div>
@endsection
