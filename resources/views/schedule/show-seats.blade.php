@extends('templates.app')

@section('content')
    <div class="container card my-5 p-4" style="margin-bottom: 10% !important">
        <div class="card-body">
            <div>
                <b> {{ $schedule['cinema']['name'] }} </b>
                {{-- now() ambil tanggal hari ini, format d (tgl)  f (nama bulan) Y (tahun) --}}
                <br>
                <b> {{ now()->format('d F, Y') }} || {{ $hour }} </b>
            </div>
            <div class="alert my-2 alert-secondary">
                <i class="fa-solid fa-info text-danger me-2"></i> Anak berusia 2 tahun keatas wajib membeli tiket
            </div>
            <div class="d-flex justify-content-center">
                <div class="row w-50">
                    <div class="col-4 d-flex ">
                        <div style="width: 20px; height: 20px; background :#112646"></div> Kursi Tersedia
                    </div>
                    <div class="col-4 d-flex ">
                        <div style="width: 20px; height: 20px; background :#eaeaea"></div> Kursi Terjual
                    </div>
                    <div class="col-4 d-flex ">
                        <div style="width: 20px; height: 20px; background :blue"></div> Kursi Dipilih
                    </div>
                </div>
            </div>
            @php
                // membuat data A-H untuk baris kursi
                $row = range('A', 'H');
                // membuat data 1-18 untuk nomor kursi
                $col = range(1, 18);
            @endphp
            @foreach ($row as $baris)
                <div class="d-flex justify-content-center">
                    @foreach ($col as $nomorKursi)
                        {{-- Jika kursi nomor 7 kasi space kosong untuk jalan kursi --}}
                        @if ($nomorKursi == 7)
                            <div style="width: 45px"></div>
                        @endif
                        <div style="background: #112646; color:white; text-align:center; padding-top:10px; width:40px; height:40px; border-radius: 10px; margin:5px"
                            onclick="selectedSeat('{{ $schedule->price }}', '{{ $baris }}', '{{ $nomorKursi }}', this)">
                            {{ $baris }}-{{ $nomorKursi }}
                        </div>
                    @endforeach
                </div>
            @endforeach
        </div>
    </div>
    <div class="fixed-bottom w-100 bg-light p-4 text-center">
        <b class="text-center">LAYAR BIOSKOP</b>
        <div class="row" style="border: 1px solid #eaeaea">
            <div class="col-6 p-4" style="border: 1px solid #eaeaea">
                <h5>Total Harga</h5>
                <h5 id="totalPrice">Rp. -</h5>
            </div>
            <div class="col-6 p-4" style="border: 1px solid #eaeaea">
                <h5>Tempat Dudug</h5>
                <h5 id="seats">Belum dipilih</h5>
            </div>
        </div>
        <div class="text-center p-2"><b>RINGKASAN ORDER</b></div>
    </div>
@endsection

@push('script')
    <script>
        // menyimpan data kursi yang dipilih 
        let seats = [];
        function selectedSeat(price, baris, nomorKursi, element) {
            // buat A-1
            let seat = baris + "-" + nomorKursi;
            // cek apakah kursi ini sudah dipilih sebelumnya, cek dari apakah ada di array seats diatas atau ngga jika ada kembalikan index nya (index01)
            let indexSeat = seats.indexOf(seat);
            // jika tidak ada berarti kursi baru dipilih. kalau gaada index nya -1
            if (indexSeat == -1) {
                // kalau gada kasi warna biru terang dan simpan data kursi ke array diatas
                element.style.background = "blue";
                seats.push(seat);
            } else {
                // jika ada, berarti ini klik kedua kali di kursi tsb. kembalikan warna ke biru tua dan hapus item dari array
                element.style.background = "#112646";
                seats.splice(indexSeat, 1);
            }

            let totalPriceElement = document.querySelector('#totalPrice')
            let seatsElement = document.querySelector('#seats')
            // hitung jumlah harga dari parameter dikali jumlah kursi yang dipilih
            let totalPrice = price * (seats.length); // length menghitung jumlah item array
            // simpan harga di element HTML
            totalPriceElement.innerText = "Rp. " + totalPrice;
            // join mengubah array menjadi string dipisahkan dengan tanda tertentu
            seatsElement.innerText = seats.join(", ");
        }
    </script>
@endpush