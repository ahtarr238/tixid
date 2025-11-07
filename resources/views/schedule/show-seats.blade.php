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
                <div class="d-flex justify-content-center w-100">
                    @foreach ($col as $nomorKursi)
                        {{-- Jika kursi nomor 7 kasi space kosong untuk jalan kursi --}}
                        @if ($nomorKursi == 7 )
                            <div style="width: 45px"></div>
                        @endif
                        <div style="background: #112646; color:white; text-align:center; padding-top:8px; width:40px; height:40px; border-radius: 8px; margin:5px"
                            onclick="selectedSeat('{{ $schedule->price }}', '{{ $baris }}', '{{ $nomorKursi }}', this)">
                            {{ $baris }}-{{ $nomorKursi }}
                        </div>
                    @endforeach
                </div>
            @endforeach
        </div>
    </div>
    <div class="fixed-bottom w-100 bg-light text-center">
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
        <input type="hidden" name="user_id" id="user_id" value="{{ Auth::user()->id}}">
        <input type="hidden" name="schedule_id" id="schedule_id" value="{{ $schedule->id }}">
        <input type="hidden" name="hours" id="hours" value="{{ $hour }}">
        <div class="text-center w-100 p-2" style="cursor: pointer;color:black;" id="btnOrder"><b>RINGKASAN ORDER</b></div>
    </div>
@endsection

@push('script')
    <script>
        // menyimpan data kursi yang dipilih 
        let seats = [];
        let totalPrice = 0;
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
            totalPrice = price * (seats.length); // length menghitung jumlah item array
            // simpan harga di element HTML
            totalPriceElement.innerText = "Rp. " + totalPrice;
            // join mengubah array menjadi string dipisahkan dengan tanda tertentu
            seatsElement.innerText = seats.join(", ");

            let btnOrder = document.querySelector("#btnOrder");
            if (seats.length >= 1) {
                btnOrder.style.background = '#112646';
                btnOrder.style.color = 'white';
                // buat agar ketika di klik mengarah ke prooses createTicket
                btnOrder.onclick = createTicket;
            } else {
                btnOrder.style.background = '';
                btnOrder.style.color = '';
                btnOrder.onclick = null ;
            }
        }

        function createTicket() {
            // AJAX (asynchronus javascript and XML)
            $.ajax({
                url: "{{ route('tickets.store') }}", // route untuk proses data
                method: "POST", // http method sesuai url
                data: {
                    // data yang mau dikirim ke route (kalo di html, input form)
                    _token: "{{ csrf_token() }}",
                    user_id: $("#user_id").val(), // value="" , dari input id="user_id"
                    schedule_id: $("#schedule_id").val(),
                    hours: $("#hours").val(),
                    quantity: seats.length,
                    total_price: totalPrice,
                    rows_of_seats: seats,
                    // fillable : value
                },
                success: function(response) {
                    // kalau berhasil, mau ngapain, data hasil disimpen di (response)
                    // console.log(response)
                    let ticketId = response.data.id;
                    window.location.href = `/tickets/${ticketId}/order`;
                },
                error: function(message) {
                    // kalau di servernya ada eror mau ngapain
                    alert("Terjadi kesalahan ketika membuat data tiket!")
                }
            })
        }
    </script>
@endpush