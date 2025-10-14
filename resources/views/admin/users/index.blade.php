@extends('templates.app')

@section('content')
    <div class="container mt-3">
        @if (Session::get('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ Session::get('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="d-flex justify-content-end mb-3 mt-4">
            <a href="{{ route('admin.users.export') }}" class="btn btn-secondary me-2">Export
            </a>
            <a href="{{ route('admin.users.create') }}" class="btn btn-success me-2">Tambah Staff
            </a>
            <a href="{{ route('admin.users.trash') }}" class="btn btn-warning"> Riwayat
            </a>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="card-title m-0">Data Staff</h5>
            </div>
            <div class="card-body">
                <table class="table table-bordered table-hover" id="staffTable">
                    <thead>
                        <tr>
                            <th class="text-center" width="5%">No</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th class="text-center" width="10%">Role</th>
                            <th class="text-center" width="20%">Aksi</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
@endsection
@push('script')
<script type="text/javascript">
$(document).ready(function() {
    $('#staffTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('admin.users.datatables') }}",
        columns: [
            { 
                data: 'DT_RowIndex',
                name: 'DT_RowIndex',
                orderable: false,
                searchable: false,
                className: 'text-center'
            },
            { data: 'name', name: 'name' },
            { data: 'email', name: 'email' },
            {  data: 'role', name: 'role',
                render: function(data) {
                    if (data === 'admin') {
                        return '<span class="badge bg-danger">Admin</span>';
                    } else if (data === 'staff') {
                        return '<span class="badge bg-primary">Staff</span>';
                    }
                    return '<span class="badge bg-secondary">User</span>';
                }
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
});
</script>
@endpush