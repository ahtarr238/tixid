@extends('templates.app')

@section('content')
    <div class="container mt-5">
        @if (Session::get('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ Session::get('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="d-flex justify-content-end mb-3">
            <button class="btn btn-success mx-2" data-bs-toggle="modal" data-bs-target="#modalAdd">Tambah Data</button>
            <a href="{{ route('staff.schedules.trash') }}" class="btn btn-warning">Riwayat</a>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="card-title m-0">Data Jadwal</h5>
            </div>
            <div class="card-body">
                <table class="table table-bordered" id="schedulesTable">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Bioskop</th>
                            <th>Film</th>
                            <th>Harga</th>
                            <th>Jam Tayang</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script type="text/javascript">
        function addInput() {
            let content = `<input type="time" name="hours[]" class="form-control mt-2">`;
            // tempat konten akan ditambahkan
            let wrap = document.querySelector("#additionalInput");
            //karna nanti akan selalu bertambah, agar yg sebelumnya tidak hilang gunakan : +=
            wrap.innerHTML += content;
        }

        $('#schedulesTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('staff.schedules.datatables') }}",
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false,
                    className: 'text-center'
                },
                {
                    data: 'cinema_name',
                    name: 'cinema.name'
                },
                {
                    data: 'movie_title',
                    name: 'movie.title'
                },
                {
                    data: 'price_formatted',
                    name: 'price'
                },
                {
                    data: 'hours_formatted',
                    name: 'hours'
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false,
                    className: 'text-center'
                }
            ]
        });
    </script>
@endpush
