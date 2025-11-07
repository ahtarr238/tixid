@extends('templates.app')

@section('content')
@if (count($schedules) >=1)
    <div class="container my-5 card">1
        <a href="{{ route('cinemas.list') }}" class="btn btn-secondary mb-4">Kembali</a>

        <div class="card-body">
            {{--  karena data schedule diambil dari get() dan data bisa lebih dari satu. maka untuk mengambil data cinema nya ambil dari 1 data saja index 0 --}}
            <i class="fa-solid fa-location-dot me-3"></i> {{ $schedules[0]['cinema']['location'] }}
            <hr>
            @foreach ($schedules as $schedule)
                <div class="my-2">
                    <div class="d-flex">
                        <div style="width: 150px; height: 200px;">
                            <img src="{{ asset('storage/' . $schedule['movie']['poster']) }}" alt=""
                                class="w-100 rounded">
                        </div>
                        <div class="ms-5 mt-4">
                            <h5 class="fw-bold">{{ $schedule['movie']['title'] }}</h5>
                            <table class="table table-borderless table-sm">
                                <tr>
                                    <td><b class="text-secondary">Genre</b></td>
                                    <td class="ps-3">{{ $schedule['movie']['genre'] }}</td>
                                </tr>
                                <tr>
                                    <td><b class="text-secondary">Durasi</b></td>
                                    <td class="ps-3">{{ $schedule['movie']['duration'] }}</td>
                                </tr>
                                <tr>
                                    <td><b class="text-secondary">Sutradara</b></td>
                                    <td class="ps-3">{{ $schedule['movie']['director'] }}</td>
                                </tr>
                                <tr>
                                    <td><b class="text-secondary">Rating Usia</b></td>
                                    <td class="ps-3"><span
                                            class="badge bg-danger">{{ $schedule['movie']['age_rating'] }}+</span></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <div class="w-100 my-3">
                        <div class="d-flex justify-content-end">
                            <div>
                                <b>Rp. {{ number_format($schedule['price'], 0, ',', '.') }}</b>
                            </div>
                        </div>
                        <div class="d-flex gap-3 ps-3 my-2">
                            {{-- this mengirimkan element html ke js untuk di manipulasi --}}
                            @foreach ($schedule['hours'] as $index => $hours)
                                <button class="btn btn-outline-secondary me-2"
                                    onclick="selectedHour('{{ $schedule->id }}', '{{ $index }}', this)">
                                    {{ $hours }} </button>
                            @endforeach
                        </div>
                    </div>
                </div>
                <hr>
            @endforeach
        </div>
    </div>
@endif
    <div class="w100 fixed-bottom bg-light text-center py-2" id="wrapBtn">
        {{-- javascript:void(0) -> nonaktifkan href --}}
        <a href="javascript:void(0)" id="btnTiket">BELI TIKET</a>
    </div>
@endsection

@push('script')
    <script>
        let btnBefore = null;

        function selectedHour(scheduleId, hourId, element) {
            // ada btnBefore (sebelumnya pernah klik btn lain)
            if (btnBefore) {
                // ubah warna btn yang di klik sebelumnya ke abu abu lagi
                btnBefore.style.background = '';
                btnBefore.style.color = '';
                btnBefore.style.borderColor = '';
            }
            // warna btn yg baru di klik
            element.style.background = '#112646';
            element.style.color = 'white';
            element.style.borderColor = '#112626';
            // update btnBefore ke element baru
            btnBefore = element;

            let wrapBtn = document.querySelector('#wrapBtn');
            let btnTiket = document.querySelector('#btnTiket');
            // warna biru di tulisan beli tiket
            wrapBtn.style.background = '#112646';
            // hapus class bg-light
            wrapBtn.classList.remove("bg-light");
            btnTiket.style.color = "white";

            // mengarahkan ke route web.php
            let url = "{{ route('schedules.seats', ['scheduleId' => ':scheduleId', 'hourId' => ':hourId']) }}".replace(
                ":scheduleId", scheduleId).replace(":hourId", hourId);
            btnTiket.href = url;
        }
    </script>
@endpush
