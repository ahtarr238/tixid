@extends('templates.app')

@section('content')
    <div class="container mt-5">
        <h5>Edit Promo</h5>
        <form action="{{ route('staff.promos.update', $promo->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="mb-3">
                <label for="promo_code" class="form-label">Kode Promo</label>
                <input type="text" class="form-control" id="promo_code" name="promo_code" value="{{ $promo->promo_code }}" required>
            </div>
            <div class="mb-3">
                <label for="discount" class="form-label">Diskon</label>
                <input type="number" class="form-control" id="discount" name="discount" value="{{ $promo->discount }}" min="1" required>
            </div>
            <div class="mb-3">
                <label for="type" class="form-label">Tipe</label>
                <select class="form-control" id="type" name="type" required>
                    <option value="percent" {{ $promo->type == 'percent' ? 'selected' : '' }}>Persen</option>
                    <option value="rupiah" {{ $promo->type == 'rupiah' ? 'selected' : '' }}>Rupiah</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="actived" class="form-label">Status</label>
                <select class="form-control" id="actived" name="actived" required>
                    <option value="1" {{ $promo->actived ? 'selected' : '' }}>Aktif</option>
                    <option value="0" {{ !$promo->actived ? 'selected' : '' }}>Non-aktif</option>
                </select>
            </div>
            <button type="submit" class="btn btn-success">Update</button>
            <a href="{{ route('staff.promos.index') }}" class="btn btn-secondary">Kembali</a>
        </form>
    </div>
@endsection
