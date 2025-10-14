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
        <a href="{{route('staff.promos.export')}}" class="btn btn-secondary mx-2"> Export
        </a>
        <a href="{{ route('staff.promos.create') }}" class="btn btn-success mx-2">Tambah Tambah
        </a>
        <a href="{{route('staff.promos.trash')}}" class="btn btn-warning"> Riwayat
        </a>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="card-title m-0">Data Promo</h5>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-hover" id="promosTable">
                <thead>
                    <tr>
                        <th width="5%">No</th>
                        <th>Kode Promo</th>
                        <th>Diskon</th>
                        <th>Tipe</th>
                        <th width="5%">Status</th>
                        <th width="30%">Aksi</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
@endsection

@push('script')
<script type="text/javascript">
$('#promosTable').DataTable({
    processing: true,
    serverSide: true,
    ajax: "{{ route('staff.promos.datatables') }}",
    columns: [
        { 
            data: 'DT_RowIndex',
            name: 'DT_RowIndex',
            orderable: false,
            searchable: false,
            className: 'text-center'
        },
        { data: 'promo_code', name: 'promo_code' },
        { data: 'discount_formatted', name: 'discount' },
        { data: 'type_formatted', name: 'type' },
        { 
            data: 'status_badge',
            name: 'actived',
            className: 'text-center'
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