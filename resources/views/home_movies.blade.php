@extends('templates.app')

@section('content')
    <div class="container my-5">
        {{-- khusus fitur searching menggunakan method get karena fungsinya tidak untuk menyimpan tapi mengambil data  --}}
        {{-- action kosong untuk diarahkan ke progress yang sama (tetap di halaman ini) --}}
        <form action="" method="GET"> 
            @csrf
            <div class="row">
                <div class="col-10">
                    <input type="text" name="search_movie" class="form-control" placeholder="Cari judul Film...">
                </div>
                <div class="col-2">
                    <button type="submit" class="btn btn-primary">cari</button>
                </div>
            </div>
        </form>
        <div class="mt-3 d-flex justify-content-center container gap-2">
            @foreach ($movies as $item)
                <div class="card shadow-sm text-center mx-1" style="width: 15rem;">
                    <img src="{{ asset('storage/' . $item['poster']) }}"class="card-img-top"
                        style="height:300px; object-fit:cover;" alt="{{ $item['title'] }}">
                    <div class="card-body">
                        <h6 class="card-title mb-2">{{ $item['title'] }}</h6>
                        <p class="small text-muted mb-2">{{ $item['genre'] }}</p>
                        <a href="{{ route('schedule.detail', $item['id']) }}" class="btn btn-primary btn-sm mx-2 mt-2">Beli tiket</a>
                        @if ($item->actived == 0)
                            <span class="badge bg-danger text-dark ms-2">Non-aktif</span>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection
