<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use App\Models\Cinema;
use App\Models\Movie;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;


class ScheduleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $cinemas = Cinema::all();
        $movies = Movie::all();

        //with()mengambil fungsi relasi dari model, untuk mengakses detail relasi ga cuma primary aja
        $schedules = Schedule::with(['cinema', 'movie'])->get();


        return view('staff.schedule.index', compact('cinemas', 'movies', 'schedules'));
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
        //
        $request->validate([
            'cinema_id' => 'required',
            'movie_id' => 'required',
            'price' => 'required|numeric',
            //karena hours arra, jd yg divalidasi itemnya -. 'hours.*'
            'hours.*' => 'required'
        ], [
            'cinema_id.required' => 'Bioskop harus dipilih',
            'movie_id.required' => 'film harus dipilih',
            'price.required' => 'harga harus diisi',
            'price.numeric' => 'harga harus diisi dengan angka',
            'hours.*.required' => 'Jam harus diisi minimal satu data jam ',
        ]);


        //ambil data jikasudah ada berdasarkan bisokop dan film yang sama
        $schedule = Schedule::where('cinema_id', $request->cinema_id)->where('movie_id', $request->movie_id)->first();
        //jika ada data yg biskop dan filmnya sama
        if ($schedule) {
            //ambil data jam yg sebelumnya
            $hours = $schedule['hours'];
        } else {
            //kalo blom ada data, hours dibuat kosong dulu
            $hours = [];
        }
        //gabungkan hours sebelumnya dengan hours baru dr input ($request->hours)
        $mergeHours = array_merge($hours, $request->hours);
        //jika ada jam yang sama, hilangkan duplikasi data
        //gunakan data jam ini untuk database
        $newHours = array_unique($mergeHours);


        //updateOrCreate : mengubah data kalo udah ada, tambah kalo belum ada
        $createdata = Schedule::updateOrCreate([
            //acuan update berdasarkan data biskop dan film yang sama
            'cinema_id' => $request->cinema_id,
            'movie_id' => $request->movie_id,
        ], [
            'price' => $request->price,
            'hours' => $newHours,
        ]);
        if ($createdata) {
            return redirect()->route('staff.schedules.index')->with('success', 'Berhasil menambahkan data');
        } else {
            return redirect()->back()->with('error', 'Gagal coba lagi!');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Schedule $schedule)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Schedule $schedule, $id)
    {
        //
        $schedule = Schedule::where('id', $id)->with(['cinema', 'movie'])->first();
        return view('staff.schedule.edit', compact('schedule'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Schedule $schedule, $id)
    {
        //
        $request->validate([
            'price' => 'required|numeric',
            'hours.*' => 'required|date_format:H:i'
        ], [
            'price.required' => 'Harga harus diisi',
            'price.numeric' => 'Harga harus diisi dengan angka',
            'hours.*.required' => 'Jam Tayang harus diisi',
            'hours.*.date_format' => 'Jam tayang harus diisi dengan format jam:menit',
        ]);

        $updateData = Schedule::where('id', $id)->update([
            'price' => $request->price,
            'hours' => $request->hours
        ]);

        if ($updateData) {
            return redirect()->route('staff.schedules.index')->with('success', 'Berhasil mengubah data');
        } else {
            return redirect()->back()->with('error', 'Gagal! coba lagi');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Schedule $schedule, $id)
    {
        Schedule::where('id', $id)->delete();
        return redirect()->route('staff.schedules.index')->with('success', 'Berhasil menghapus data');
    }

    public function trash()
    {

        // ORM yang digunakan terkait softdeletes
        // onlyTrashed() -> filter data yag sudah dihapus, delete_at BUKAN NULL
        // restore() -> mengembalikan data yang sudah dihapus (menghapus nilai tanggal pada deleted_at)
        // forceDelete() -> menghapus data secara permanen, data dihilangkan bahkan dari databasenya

        $scheduleTrash = Schedule::with(['cinema', 'movie'])->onlyTrashed()->get();
        return view('staff.schedule.trash', compact('scheduleTrash'));
    }

    public function restore($id)
    {
        $schedule = Schedule::onlyTrashed()->find($id);
        $schedule->restore();
        return redirect()->route('staff.schedules.index')->with('Success', 'Berhasil mengembalikan data!');
    }

    public function deletePermanent($id)
    {
        $schedule = Schedule::onlyTrashed()->find($id);
        $schedule->forceDelete();
        return redirect()->back()->with('Success', 'Berhasil menghapus data secara permanen!');
    }

    public function datatables()
    {
        $schedules = Schedule::with(['cinema', 'movie'])->get();

        return DataTables::of($schedules)
            ->addIndexColumn()
            ->addColumn('cinema_name', function ($schedule) {
                return $schedule->cinema->name ?? '-';
            })
            ->addColumn('movie_title', function ($schedule) {
                return $schedule->movie->title ?? '-';
            })
            ->addColumn('price_formatted', function ($schedule) {
                return 'Rp ' . number_format($schedule->price, 0, ',', '.');
            })
            ->addColumn('hours_formatted', function ($schedule) {
                return implode(', ', $schedule->hours);
            })
            ->addColumn('action', function ($schedule) {
                $actions = '<div class="d-flex justify-content-center gap-2">';

                // Edit button
                $actions .= '<a href="' . route('staff.schedules.edit', $schedule->id) . '" class="btn btn-warning btn-sm">Edit</a>';

                // Delete button
                $actions .= '<form action="' . route('staff.schedules.delete', $schedule->id) . '" method="POST">
                            ' . csrf_field() . '' . method_field('DELETE') . '<button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                        </form>';

                $actions .= '</div>';
                return $actions;
            })
            ->rawColumns(['action'])
            ->make(true);
    }
}
