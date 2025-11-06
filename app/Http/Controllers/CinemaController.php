<?php

namespace App\Http\Controllers;

use App\Models\Cinema;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\excel;
use App\Exports\CinemaExport;
use Yajra\DataTables\Facades\DataTables;


class CinemaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $cinemas = Cinema::all();
        return view('admin.cinema.index', compact('cinemas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|min:3',
            'location' => 'required|min:3',
        ] , [ 
            'name.required' => 'Nama bioskop wajib diisi',
            'name.min' => 'Nama bioskop minimal 3 karakter',
            'location.required' => 'Alamat bioskop wajib diisi',
            'location.min' => 'Alamat bioskop minimal 3 karakter',
        ]);

        $createCinema = Cinema::create([
            'name' => $request->name,
            'location' => $request->location,
        ]);

        if($createCinema){
            return redirect()->route('admin.cinemas.index')->with('success', 'Data bioskop berhasil ditambahkan');
        } else {
            return redirect()->back()->with('error', 'Data bioskop gagal ditambahkan');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Cinema $cinema)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $cinema = Cinema::find($id);
        return view('admin.cinema.edit', compact('cinema'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|min:3',
            'location' => 'required|min:3',
        ] , [ 
            'name.required' => 'Nama bioskop wajib diisi',
            'name.min' => 'Nama bioskop minimal 3 karakter',
            'location.required' => 'Alamat bioskop wajib diisi',
            'location.min' => 'Alamat bioskop minimal 3 karakter',
        ]);

        $updateCinema = Cinema::where('id', $id)->update([
            'name' => $request->name,
            'location' => $request->location,
        ]);

        if($updateCinema){
            return redirect()->route('admin.cinemas.index')->with('success', 'Data bioskop berhasil diupdate');
        } else {
            return redirect()->back()->with('error', 'Data bioskop gagal diupdate');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        // count() : menghitung jumlah data yang didapat 
        $schedules = Schedule::where('cinema_id', $id)->count();

        if($schedules) {
            return redirect()->route('admin.cinemas.index')->with('error', 'Data dapat menghapus karena ada jadwal yang terkait dengan bioskop ini');
        }

        $deleteData = Cinema::where('id', $id)->delete();

        if($deleteData){
            return redirect()->route('admin.cinemas.index')->with('success', 'Data bioskop berhasil dihapus');
        } else {
            return redirect()->back()->with('error', 'Data bioskop gagal dihapus');
        }
    }
    public function export()
    {
        // nama file yang akan di unduh
        $fileName = 'data-bioskop.xlsx';
        // proses unduh
        return excel::download(new CinemaExport, $fileName);
    }

    // Recycle Bin Functions
    public function trash()
    {
        $cinemas = Cinema::onlyTrashed()->paginate(10);
        return view('admin.cinema.trash', compact('cinemas'));
    }

    public function restore($id)
    {
        $cinema = Cinema::onlyTrashed()->findOrFail($id);
        if ($cinema->restore()) {
            return redirect()->route('admin.cinemas.trash')->with('success', 'Data bioskop berhasil dikembalikan.');
        } else {
            return redirect()->route('admin.cinemas.trash')->with('error', 'Data bioskop gagal dikembalikan.');
        }
    }

    public function deletePermanent($id)
    {
        $cinema = Cinema::onlyTrashed()->findOrFail($id);
        if ($cinema->forceDelete()) {
            return redirect()->route('admin.cinemas.trash')->with('success', 'Data bioskop telah dihapus permanen.');
        } else {
            return redirect()->route('admin.cinemas.trash')->with('error', 'Data bioskop gagal dihapus permanen.');
        }
    }

    public function restoreAll()
    {
        try {
            Cinema::onlyTrashed()->restore();
            return redirect()->route('admin.cinemas.trash')->with('success', 'Semua data bioskop berhasil dikembalikan.');
        } catch (\Exception $e) {
            return redirect()->route('admin.cinemas.trash')->with('error', 'Gagal mengembalikan semua data bioskop: ' . $e->getMessage());
        }
    }

    public function deleteAllPermanent()
    {
        try {
            Cinema::onlyTrashed()->forceDelete();
            return redirect()->route('admin.cinemas.trash')->with('success', 'Semua data bioskop telah dihapus permanen.');
        } catch (\Exception $e) {
            return redirect()->route('admin.cinemas.trash')->with('error', 'Gagal menghapus semua data bioskop: ' . $e->getMessage());
        }
    }

    public function datatables()
    {
        $cinemas = Cinema::query();

        return DataTables::of($cinemas)
            ->addIndexColumn()
            ->addColumn('action', function ($cinema) {
                $btnEdit = '<a href="' . route('admin.cinemas.edit', $cinema->id) . '" class="btn btn-primary">Edit</a>';
                $btnDelete = '
                <form action="' . route('admin.cinemas.delete', $cinema->id) . '" method="POST" style="display:inline-block;">
                    ' . csrf_field() . method_field('DELETE') . '
                    <button type="submit" class="btn btn-danger">Hapus</button>
                </form>
            ';
                return '<div class="d-flex justify-content-center gap-2">' . $btnEdit . $btnDelete . '</div>';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function cinemaList() {
        $cinemas = Cinema::all();
        return view('schedule.cinemas', compact('cinemas'));
    }

    public function cinemaSchedules($cinema_id) {
        $schedules = Schedule::where('cinema_id', $cinema_id)->with('movie')->whereHas('movie', function($q) {
            $q->where('actived', 1);
        })->get();
        return view('schedule.cinema-schedules', compact('schedules'));
    }
}