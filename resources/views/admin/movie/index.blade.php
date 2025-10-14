@extends('templates.app')

@section('content')
    <div class="container mt-5">
        @if (Session::get('success'))
            <div class="alert alert-success">{{ Session::get('success') }}</div>
        @endif
        <div class="d-flex justify-content-end">
            <a href="{{ route('admin.movies.export') }}" class="btn btn-secondary mx-2">Export .xslx</a>
            <a href="{{ route('admin.movies.create') }}" class="btn btn-success">Tambah Film</a>
            <a href="{{ route('admin.movies.trash') }}" class="btn btn-primary mx-2">Riwayat</a>
        </div>
        <h5 class="mt-3">Data Film</h5>
        <table class="table table-bordered" id="moviesTable">
            <tr>
                <th class="col-1 text-center"></th>
                <th class="col-1 text-center">Poster</th>
                <th class="col-2 text-center">Judul Film</th>
                <th class="col-1 text-center">Status</th>
                <th class="col-4 text-center">Aksi</th>
            </tr>
            @foreach ($movies as $key => $item)
                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td class="text-center">
                        <img src="{{ asset('storage/' . $item['poster']) }}" width="120">
                    </td>
                    <td>{{ $item['title'] }}</td>
                    <td class="text-center">
                        @if ($item['actived'] == 1)
                            <span class="badge bg-success text-center">Aktif</span>
                        @else
                            <span class="badge bg-secondary text-center">Non-aktif</span>
                        @endif
                    </td>
                    <td class="d-flex justify-content-center ">
                        {{-- onclick : fungsi javascript ketika komponen di klik  --}}
                        <button class="btn btn-secondary me-2 mx-2" onclick="showModal({{ $item }})">
                            Detail</button>
                        <a href="{{ route('admin.movies.edit', $item['id']) }}" class="btn btn-primary mx-2">Edit</a>
                        <form action="{{ route('admin.movies.destroy', $item->id) }}" method="POST"
                            onsubmit="return confirm('Yakin ingin menghapus?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger mx-2">Hapus</button>
                        </form>
                        {{-- jika actived == true  --}}
                        <form action="{{ route('admin.movies.actived', $item->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('PATCH')
                            @if ($item['actived'] == 1)
                                <button type="submit" class="btn btn-warning mx-2">Non-aktif</button>
                            @endif

                        </form>
                    </td>
                </tr>
            @endforeach
        </table>
        <div class="modal fade" id="modalDetail" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="exampleModalLabel">Detail Film</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" id="modalDetailBody">
                        ...
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        function showModal(item) {
            // console.log(item);
            let image = "{{ asset('storage/') }}" + "/" + item.poster;
            // backtip ('') ; membuta string yang bisa di ente
            let content = `
       <div class="d-block mx-auto my-2">
        <img src="${image}" width="120">
        </div>
       <ol>
        <li>Judul : ${item.title}</li>
        <li>Durasi : ${item.duration}</li>
        <li>Genre : ${item.genre}</li>
        <li>Sutradara : ${item.director}</li>
        <li>Usia Minimal : <span class="badge badge-danger">${item.age_rating} + </span></li>
        <li>Sinopsis : ${item.description}</li>
        </ol>`;
            //memanggil variable pada tanda '' pake $()
            // memanggil element HTML yang akan disimpan kontent diatas -> document.querySelector
            // InnerHTML -> mengisi kontent html
            document.querySelector('#modalDetailBody').innerHTML = content;
            new bootstrap.Modal(document.querySelector('#modalDetail')).show();

        }

        $(function() {
            $('#moviesTable').DataTable({
                processing: true,
                // Data untuk datatable diproses secara serverside (controller)
                severSide: true,
                // routing menuju fungsi yang memproses data untuk datatable
                ajax: "{{route('admin.movies.datatables')}}",
                // urutan column (td), pastikan urutan sesuai th
                // data: 'nama' -> nama diambil dari rawColumn jika addColumns, atau field dari model fillable
                columns: [
                    {   data : 'DT_RowIndex', name: 'DT_RowIndex', orderable:false, searchable: false   },
                    {   data : 'poster_img', name: 'poster_img', orderable:false, searchable: false   },
                    {   data : 'title', name: 'title'},
                    {   data : 'actived_badge', name: 'actived_badge', orderable:false, searchable: false   },
                    {   data : 'action', name: 'action'},
                ]
            });
        })
    </script>
@endpush
