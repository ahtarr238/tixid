<?php

namespace App\Http\Controllers;

use App\Models\Promo;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\excel;
use App\Exports\PromoExport;
use Yajra\DataTables\Facades\DataTables;

class PromoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $promos = Promo::all();
        return view('staff.promo.index', compact('promos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('staff.promo.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'promo_code' => 'required|string|unique:promos',
            'discount' => 'required|integer|min:1',
            'type' => 'required|in:percent,rupiah',
        ]);
        $data = $request->all();
        $data['actived'] = 1;
        Promo::create($data);
        return redirect()->route('staff.promos.index')->with('success', 'Promo created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(Promo $promo)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $promo = Promo::findOrFail($id);
        return view('staff.promo.edit', compact('promo'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'promo_code' => 'required|string|unique:promos,promo_code,' . $id,
            'discount' => 'required|integer|min:1',
            'type' => 'required|in:percent,rupiah',
            'actived' => 'required|boolean',
        ]);

        $promo = Promo::findOrFail($id);
        $promo->update($request->all());

        return redirect()->route('staff.promos.index')->with('success', 'Promo updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $promo = Promo::findOrFail($id);
        $promo->delete();

        return redirect()->route('staff.promos.index')->with('success', 'Promo deleted successfully');
    }

    public function actived($id)
    {
        //ngambil data film dari database lewat id 
        $promo = Promo::find($id);
        //kalo status actived maka bernilai 1(true) jika di nonaktif ubah jadi 0 
        $newStatus = $promo->actived == 1 ? 0 : 1;

        //menyimpan perubahan status
        $promo->actived = $newStatus;
        $promo->save();

        return redirect()->route('staff.promos.index')->with('success', 'Status film berhasil diubah');
    }

    public function export()
    {
        // nama file yang akan di unduh
        $fileName = 'data-promo.xlsx';
        // proses unduh
        return excel::download(new PromoExport, $fileName);
    }

    public function trash()
    {

        // ORM yang digunakan terkait softdeletes
        // onlyTrashed() -> filter data yag sudah dihapus, delete_at BUKAN NULL
        // restore() -> mengembalikan data yang sudah dihapus (menghapus nilai tanggal pada deleted_at)
        // forceDelete() -> menghapus data secara permanen, data dihilangkan bahkan dari databasenya

        $promoTrash = Promo::onlyTrashed()->get();
        return view('staff.promo.trash', compact('promoTrash'));;
    }

    public function restore($id)
    {
        $promo = Promo::onlyTrashed()->find($id);
        $promo->restore();
        return redirect()->route('staff.promos.index')->with('Success', 'Berhasil mengembalikan data!');
    }

    public function deletePermanent($id)
    {
        $promo = Promo::onlyTrashed()->find($id);
        $promo->forceDelete();
        return redirect()->back()->with('Success', 'Berhasil menghapus data secara permanen!');
    }

    public function datatables()
    {
        $promos = Promo::query();

        return DataTables::of($promos)
            ->addIndexColumn()
            ->addColumn('discount_formatted', function ($promo) {
                return $promo->type === 'percent' ?
                    $promo->discount . '%' :
                    'Rp ' . number_format($promo->discount, 0, ',', '.');
            })
            ->addColumn('type_formatted', function ($promo) {
                return ucfirst($promo->type);
            })
            ->addColumn('status_badge', function ($promo) {
                return $promo->actived == 1 ?
                    '<span class="badge bg-success">Aktif</span>' :
                    '<span class="badge bg-secondary">Non-aktif</span>';
            })
            ->addColumn('action', function ($promo) {
                $actions = '';

                $actions .= '<a href="' . route('staff.promos.edit', $promo->id) . '" class="btn btn-primary btn-sm"> Edit</a>';

                $statusBtn = $promo->actived == 1 ?
                    '<button type="submit" class="btn btn-warning btn-sm"> Non-aktifkan</button>' :
                    '<button type="submit" class="btn btn-success btn-sm"> Aktifkan</button>';

                $actions .= '<form action="' . route('staff.promos.actived', $promo->id) . '" method="POST" class="d-inline mx-2">
                            ' . csrf_field() . '' . method_field('PATCH') . '' . $statusBtn . '</form>';

                // Delete button
                $actions .= '<form action="' . route('staff.promos.delete', $promo->id) . '" method="POST" class="d-inline">' . csrf_field() . '' . method_field('DELETE') . '
                            <button type="submit" class="btn btn-danger btn-sm"> Hapus
                            </button>
                            </form>';

                return $actions;
            })
            ->rawColumns(['status_badge', 'action'])
            ->make(true);
    }
}
