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

    <!-- Modal Tambah Data -->
    <div class="modal fade" id="modalAdd" tabindex="-1" aria-labelledby="modalAddLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalAddLabel">Tambah Jadwal Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formAdd" action="{{ route('staff.schedules.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="cinema_id" class="form-label">Bioskop</label>
                            <select class="form-select" id="cinema_id" name="cinema_id" required>
                                <option value="" selected disabled>Pilih Bioskop</option>
                                @foreach($cinemas as $cinema)
                                    <option value="{{ $cinema->id }}">{{ $cinema->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="movie_id" class="form-label">Film</label>
                            <select class="form-select" id="movie_id" name="movie_id" required>
                                <option value="" selected disabled>Pilih Film</option>
                                @foreach($movies as $movie)
                                    <option value="{{ $movie->id }}">{{ $movie->title }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="price" class="form-label">Harga</label>
                            <input type="number" class="form-control" id="price" name="price" min="0" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Jam Tayang</label>
                            <div id="additionalInput">
                                <input type="time" name="hours[]" class="form-control mt-2" required>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-primary mt-2" onclick="addInput()">
                                <i class="fas fa-plus"></i> Tambah Jam
                            </button>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script type="text/javascript">
        function addInput() {
            let content = `<div class="input-group mt-2">
                <input type="time" name="hours[]" class="form-control" required>
                <button type="button" class="btn btn-outline-danger" onclick="removeInput(this)">
                    <i class="fas fa-trash"></i>
                </button>
            </div>`;
            document.querySelector("#additionalInput").innerHTML += content;
        }

        function removeInput(button) {
            button.closest('.input-group').remove();
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

        // Reset form saat modal ditutup
        document.getElementById('modalAdd').addEventListener('hidden.bs.modal', function () {
            document.getElementById('formAdd').reset();
            document.getElementById('additionalInput').innerHTML = 
                '<input type="time" name="hours[]" class="form-control mt-2" required>';
        });
    </script>
@endpush