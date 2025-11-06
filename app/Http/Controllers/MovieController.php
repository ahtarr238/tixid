<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\excel;
use App\Exports\MovieExport;
use Yajra\DataTables\Facades\DataTables;

class MovieController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $movies = Movie::all();
        return view('admin.movie.index', compact('movies'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.movie.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // dd($request->all());
        $request->validate(
            [
                'title' => 'required',
                'duration' => 'required',
                'genre' => 'required',
                'director' => 'required',
                'age_rating' => 'required|numeric',
                //mimes -> bentuk file yang diizinkan untuk upload
                'poster' => 'required|mimes:jpg,jpeg,png,webp,svg',
                'description' => 'required|min:10'
            ],
            [
                'title.required' => 'Judul film harus diisi',
                'duration.required' => 'Durasi film harus diisi',
                'genre.required' => 'Genre film harus diisi',
                'director.required' => 'Sutradara film harus diisi',
                'age_rating.required' => 'Usia minimal harus diisi',
                'age_rating.numeric' => 'Usia minimal harus diisi dengan angka', //numeric memastikan berbentuk angka
                'poster.required' => 'Poster harus diisi',
                'poster.mimes' => 'Poster harus diisi dengan JPG/JPEG/PNG/WEBP/SVG',
                'description.required' => 'Sinopsis film harus diisi',
                'description.min' => 'Sinopsis film harus diisi minimal 10 karakter',
            ]
        );
        //ambil file yang di upload = $request->file('name_input')
        $gambar = $request->file('poster');
        //buat nama baru di filenya agar menghindari nama file yang sama
        //nama file yang diinginkan = <random>-poster.png
        //getClientOriginalExtension() = mengambil ekstensi file (png/jpg/dll)
        $namaGambar = Str::random(5) . "-poster." . $gambar->getClientOriginalExtension();
        //simpan file ke storage, nama file gunakan nama file baru
        $path = $gambar->storeAs('poster', $namaGambar, 'public');

        $createData = Movie::create([
            'title' => $request->title,
            'duration' => $request->duration,
            'genre' => $request->genre,
            'director' => $request->director,
            'age_rating' => $request->age_rating,
            'poster' => $path,   //path ke lokasi file yang disimpan dari storeAs()
            'description' => $request->description,
            'actived' => 1
        ]);

        if ($createData) {
            return redirect()->route('admin.movies.index')->with('success', 'Berhasil membuat data film');
        } else {
            return redirect()->back()->with('error', 'Gagal silahkan coba lagi');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Movie $movie)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $movie = Movie::find($id);
        return view('admin.movie.edit', compact('movie'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate(
            [
                'title' => 'required',
                'duration' => 'required',
                'genre' => 'required',
                'director' => 'required',
                'age_rating' => 'required|numeric',
                //mimes -> bentuk file yang diizinkan untuk upload
                'poster' => 'mimes:jpg,jpeg,png,webp,svg',
                'description' => 'required|min:10'
            ],
            [
                'title.required' => 'Judul film harus diisi',
                'duration.required' => 'Durasi film harus diisi',
                'genre.required' => 'Genre film harus diisi',
                'director.required' => 'Sutradara film harus diisi',
                'age_rating.required' => 'Usia minimal harus diisi',
                'age_rating.numeric' => 'Usia minimal harus diisi dengan angka', //numeric memastikan berbentuk angka
                'poster.mimes' => 'Poster harus diisi dengan JPG/JPEG/PNG/WEBP/SVG',
                'description.required' => 'Sinopsis film harus diisi',
                'description.min' => 'Sinopsis film harus diisi minimal 10 karakter',
            ]
        );
        $movie = Movie::find($id);
        if ($request->file('poster')) {
            $fileSebelumnya = storage_path('app/public/' . $movie['poster']);
            if (file_exists($fileSebelumnya)) {
                unlink($fileSebelumnya);
            }

            //ambil file yang di upload = $request->file('name_input')
            $gambar = $request->file('poster');
            //buat nama baru di filenya agar menghindari nama file yang sama
            //nama file yang diinginkan = <random>-poster.png
            //getClientOriginalExtension() = mengambil ekstensi file (png/jpg/dll)
            $namaGambar = Str::random(5) . "-poster." . $gambar->getClientOriginalExtension();
            //simpan file ke storage, nama file gunakan nama file baru
            $path = $gambar->storeAs('poster', $namaGambar, 'public');
        }


        $updateData = Movie::where('id', $id)->update([
            'title' => $request->title,
            'duration' => $request->duration,
            'genre' => $request->genre,
            'director' => $request->director,
            'age_rating' => $request->age_rating,
            // ?? sebelum ?? (if) setelah ?? (else)
            //kalau ada $path (poster baru), ambil data baru. kalau tidak ada, ambil dari data $movie sebelumnya
            'poster' => $path ?? $movie['poster'],
            'description' => $request->description,
            'actived' => 1
        ]);

        if ($updateData) {
            return redirect()->route('admin.movies.index')->with('success', 'Berhasil mengubah data film');
        } else {
            return redirect()->back()->with('error', 'Gagal silahkan coba lagi');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {

        // count() : menghitung jumlah data yang didapat 
        $schedules = Schedule::where('movie_id', $id)->count();

        if ($schedules) {
            return redirect()->route('admin.movies.index')->with('error', 'Data dapat menghapus karena ada jadwal yang terkait dengan bioskop ini');
        }

        $film = Movie::find($id);

        if($film) {
            if ($film->poster && Storage::disk('public')->exists($film->poster)) {
                Storage::disk('public')->delete($film->poster);
            }
        }


    $deleteData = Movie::where('id', $id)->delete();
        if($deleteData){
            return redirect()->route('admin.movies.index')->with('success', 'Data film berhasil dihapus');
        }else {
            return redirect()->back()->with('error', 'Data film gagal dihapus');
        }
    }

    public function home() {
        //where (field, value) -> mencari data
        // get() -> mengambil semua data dari hasil filter
        // mengurutkan -> orderby('field', 'asc/desc' : ASC(a-z 0-9 terbaru ke terlama),  DESC(z-a, 9-0 terlama ke terbaru))
        // limit(angka) mengambil sejumlah yang ditentukan
        $movies = Movie::where('actived', 1)->orderby('created_at', 'DESC')->limit(4)->get();
        return view('home', compact('movies'));
    }

    public function homeAllMovies(Request $request) {
        // ambil value input search name="search_movie"
        $title = $request->search_movie;
        // cek jika input ada isinya maka ari data
        if ($title !="" )  {
            // LIKE (seperti) : mencari data yang mengandung kata tertentu 
            // % depan : mencari kata belakang, % belakang : mencari kata depan, % depan belakang : mencari kata di depan dan belakang
            $movies = Movie::where('title', 'LIKE', '%' . $title . '%')->where('actived', 1)->orderby('created_at', 'DESC')->get();
        }else {
            $movies = Movie::where('actived', 1)->orderBy('created_at','DESC')->get();
        }
        return view('home_movies', compact('movies'));
    }

    public function actived($id)
    {
        //ngambil data film dari database lewat id 
        $movie = Movie::find($id);
        //kalo status actived maka bernilai 1(true) jika di nonaktif ubah jadi 0 
        $newStatus = $movie->actived == 1 ? 0 : 1;

        //menyimpan perubahan status
        $movie->actived = $newStatus;
        $movie->save();

        return redirect()->route('admin.movies.index')->with('success', 'Status film berhasil diubah');
    }

    public function export() {
        // nama file yang akan di unduh
        $fileName = 'data-film.xlsx';
        // proses unduh
        return excel::download(new MovieExport, $fileName);
    }

    public function movieSchedules($movie_id, Request $request){
        $priceSort = $request->price;
        if ($priceSort) {
            // karena price adanya di schedules bukan moviem jadi urutkan datanya dai schedules (relasi)
            $movie = Movie::where('id', $movie_id)->with(['schedules' => function ($q) use ($priceSort) {
            // 'schedules' => function($q) melakukan filter pada relasi
            // $q mewakilkan model schedules
            $q->orderBy('price', $priceSort);
            }, 'schedules.cinema'])->first();
        }else {
            $movie = Movie::where('id', $movie_id)->with(['schedules'], 'schedules.cinema')->first();
        }

        return view('schedule.detail-film', compact('movie'));
    }

    public function trash()
{
    $movieTrash = Movie::onlyTrashed()->get();
    return view('admin.movie.trash', compact('movieTrash'));
}

public function restore($id)
{
    $movie = Movie::onlyTrashed()->find($id);
    $movie->restore();
    return redirect()->route('admin.movies.index')
        ->with('success', 'Film berhasil dikembalikan!');
}

public function deletePermanent($id)
{
    $movie = Movie::onlyTrashed()->find($id);
    // Delete old poster if exists
    if ($movie->poster && Storage::exists('public/' . $movie->poster)) {
        Storage::delete('public/' . $movie->poster);
    }
    $movie->forceDelete();
    return redirect()->back()
        ->with('success', 'Film berhasil dihapus permanen!');
}

public function datatables() {
    $movies = Movie::query();
    return DataTables::of($movies)->addIndexColumn()->addColumn('poster_img', function($movie) {
        $url = asset('storage/' . $movie->poster);
        return '<img src="' . $url . '"width="70">';
    })->addColumn('actived_badge', function($movie) {
        if ($movie->actived) {
            return '<span class="badge badge-success">Aktif</span>';
        }else {
            return '<span class="badge badge-danger">Non-Aktif</span>';
        }
    })->addColumn('action', function($movie){
        $btnDetail = '<button type="button" class="btn btn-secondary" onclick= \'showModal(' . json_encode($movie) . ')\'>Detail</button>';
        $btnEdit = '<a href="' . route('admin.movies.edit', $movie->id) . '" class="btn btn-primary">Edit</a>';
        $btnDelete = '<form action="' . route('admin.movies.destroy', $movie->id) . '" method="POST" style="display:inline-block">
        ' . csrf_field() . '' . method_field('DELETE') . '<button type="submit" class="btn btn-danger">Hapus</button> </form>';
        $btnNonAktif = '';
        if ($movie->actived) {
            $btnNonAktif = '<form action="' . route('admin.movies.actived', $movie->id) . '" method="POST" style="display:inline-block">
        ' . csrf_field() . '' . method_field('PATCH') . '<button type="submit" class="btn btn-warning">Non-Aktif</button> </form>';
        }
        return '<div class="d-flex justify-content-center align-items-center gap-2">' . $btnDetail . $btnEdit . $btnDelete . $btnNonAktif . '</div>';
    })->rawColumns(['poster_img', 'actived_badge', 'action'])->make(true);
}

}
