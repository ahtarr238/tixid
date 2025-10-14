@extends('templates.app')

@section('content')
    @if (Session::get('success'))
        <div class="alert alert-success">{{ Session::get('success') }}</div>
    @endif
    <div class="container mt-3">
        @if (Session::get('error'))
            <div class="alert alert-danger">{{ Session::get('error') }}</div>
        @endif

        <div class="d-flex justify-content-end mb-3 mt-4">
            <a href="{{ route('admin.cinemas.trash') }}" class="btn btn-secondary">Data Sampah</a>
            <a href="{{ route('admin.cinemas.export') }}" class="btn btn-secondary mx-2">Export .xslx</a>
            <a href="{{ route('admin.cinemas.create') }}" class="btn btn-success">Tambah Data</a>
        </div>

        <h5>Data Bioskop</h5>
        <table id="cinemasTable" class="table my-3 table-bordered">
            <thead>
                <tr>
                    <th class="text-center col-1">No</th>
                    <th>Nama Bioskop</th>
                    <th>Lokasi Bioskop</th>
                    <th class="text-center col-3">Aksi</th>
                </tr>
            </thead>
        </table>
    </div>
@endsection

@push('script')
    <script>
        $(function() {
            $('#cinemasTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('admin.cinemas.datatables') }}",
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: "text-center" },
                    { data: 'name', name: 'name' },
                    { data: 'location', name: 'location' },
                    { data: 'action', name: 'action', orderable: false, searchable: false, className: "text-center" },
                ]
            });
        });
    </script>
@endpush