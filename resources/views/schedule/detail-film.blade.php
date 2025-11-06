@extends('templates.app')

@section('content')
    <div class="container pt-5">
        <a href="{{ route('home.movies') }}" class="btn btn-secondary mb-4">Kembali</a>

        <div class="w-75 d-block m-auto">
            {{-- Poster + Detail Film --}}
            <div class="d-flex">
                <div style="width: 150px; height: 200px;">
                    <img src="{{ asset('storage/' . $movie->poster) }}" alt="{{ $movie->title }}" class="w-100 rounded">
                </div>
                <div class="ms-5 mt-4">
                    <h5 class="fw-bold">{{ $movie->title }}</h5>
                    <table class="table table-borderless table-sm">
                        <tr>
                            <td><b class="text-secondary">Genre</b></td>
                            <td class="ps-3">{{ $movie->genre }}</td>
                        </tr>
                        <tr>
                            <td><b class="text-secondary">Durasi</b></td>
                            <td class="ps-3">{{ $movie->duration }}</td>
                        </tr>
                        <tr>
                            <td><b class="text-secondary">Sutradara</b></td>
                            <td class="ps-3">{{ $movie->director }}</td>
                        </tr>
                        <tr>
                            <td><b class="text-secondary">Rating Usia</b></td>
                            <td class="ps-3"><span class="badge bg-danger">{{ $movie->age_rating }}+</span></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        <div class="d-flex justify-content-center flex-column align-items-center ">
            @php
                if (request()->get('price')) {
                    $activeTab = true;
                    // kalau sudah pernah sortir price dan typenya ASC berubah menjadi DESC
                    if (request()->get('price') == 'ASC') {
                        $typePrice = 'DESC';
                    } else {
                        // Kalau sebelumnya bukan ASC (berarti DESC) type sortir jadi ASC
                        $typePrice = 'ASC';
                    }
                } else {
                    $activeTab = false;
                    // kalau belum pernah sortir (belum ada ?price=) berarti typenya ASC
                    $typePrice = 'ASC';
                }
            @endphp
            <ul class="nav nav-underline">
                <li class="nav-item ">
                    <button class="nav-link {{ $activeTab == false ? 'show active' : '' }}" aria-current="page"
                        data-bs-toggle="tab" data-bs-target="#sinopsis-tab-pane">Sinopsis</button>
                </li>
                <li class="nav-item ">
                    <button class="nav-link {{ $activeTab == true ? 'show active' : '' }}" data-bs-toggle="tab"
                        data-bs-target="#jadwal-tab-pane">Jadwal</button>
                </li>
            </ul>
            <div class="tab-content" id="myTabContent">
                <div class="tab-pane fade {{ $activeTab == false ? 'show active' : '' }}" id="sinopsis-tab-pane"
                    role="tabpanel" aria-labelledby="home-tab" tabindex="0">
                    <div class="mt-3 w-75 d-block mx-auto" style="text-align: justify">
                        {{ $movie['description'] }}
                    </div>
                </div>
                <div class="tab-pane fade {{ $activeTab == true ? 'show active' : '' }}" id="jadwal-tab-pane"
                    role="tabpanel" aria-labelledby="profile-tab" tabindex="0">
                    <div class="dropdown my-3 w-100">
                        <button class="btn btn-secondary dropdown-toggle w-100" type="button"
                            id="dropdownMenuButton"data-mdb-dropdown-init data-mdb-ripple-init
                            aria-expanded="false">sortir</button>
                        <ul class="dropdown-menu w-100 text-center" aria-labelledby="dropdownMenuButton">
                            <li><a class="dropdown-item " href="?price={{ $typePrice }}">Harga</a></li>
                            <li><a class="dropdown-item " href="#">Alfabet</a></li>
                        </ul>
                    </div>
                    @foreach ($movie['schedules'] as $schedule)
                        <div class="w-100 p-2 border border-black mt-2">
                            <div class="d-flex justify-content-between">
                                <span>
                                    <i class="fa-solid fa-building"></i><b> {{ $schedule['cinema']['name'] }} </b>
                                </span>
                                <div>
                                    Rp. {{ number_format($schedule['price'], 0, ',', '.') }}
                                </div>
                            </div>
                            <div class="ms-3"> {{ $schedule['cinema']['location'] }} </div>
                            <br>
                            <div class="d-flex d-wrap">
                                {{-- this mengirimkan element html ke js untuk di manipulasi --}}
                                @foreach ($schedule['hours'] as $index => $hours)
                                    <button class="btn btn-outline-secondary me-2"
                                        onclick="selectedHour('{{ $schedule->id }}', '{{ $index }}', this)">
                                        {{ $hours }} </button>
                                @endforeach
                            </div>
                            <hr>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
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
                btnBefore.style.background = '' ;
                btnBefore.style.color = '' ;
                btnBefore.style.borderColor = '' ;
            }
            // warna btn yg baru di klik
                element.style.background = '#112646' ;
                element.style.color = 'white' ;
                element.style.borderColor = '#112626' ;
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
                let url = "{{ route('schedules.seats', ['scheduleId' => ':scheduleId', 'hourId' => ':hourId']) }}".replace(":scheduleId", scheduleId).replace(":hourId", hourId);
                btnTiket.href = url;
        }
    </script>
@endpush