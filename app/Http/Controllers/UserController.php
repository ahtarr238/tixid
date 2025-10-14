<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\excel;
use App\Exports\UserExport;
use Yajra\DataTables\Facades\DataTables;



class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::all();
        return view('admin.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.users.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|min:3',
            'email' => 'required|email:dns',
            'password' => 'required|min:8',
        ],[
            'name.required' => 'Nama wajib di isi',
            'email.required' => 'Alamat email wajib diisi dengan email yang valid',
            'password.required' => 'Password harus diisi minimal 8 karakter',
        ]);

        $CreateUser = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'staff'
        ]);

        if($CreateUser){
            return redirect()->route('admin.users.index')->with('success', 'Data berhasil di tambahkan');
        } else {
            return redirect()->route('admin.users.index')->with('error', 'Data gagal di tambahkan, silahkan coba lagi');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $user = User::find($id);
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request,$id)
    {
        $request->validate([
            'name' => 'required|min:3',
            'email' => 'required|email:dns',
            'password' => 'required|min:8',
        ], [
            'name.required' => 'Nama  wajib diisi',
            'name.min' => 'Nama minimal 3 karakter',
            'email.required' => 'Email wajib diisi',
            'email.min' => 'Email wajib diisi dengan data yang valid',
            'password.required' => 'password wajib diisi',
            'password.min' => 'Password wajib diisi minimal 8 karakter',
        ]);

        $updateUser = User::where('id', $id)->update([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'staff'
        ]);

        if ($updateUser) {
            return redirect()->route('admin.users.index')->with('Success', 'Staff berhasil di update');
        } else {
            return redirect()->route('admin.users.index')->with('error', 'Staff gagal di update');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $deleteData = User::where('id', $id)->delete();
        if($deleteData){
            return redirect()->route('admin.users.index')->with('success', 'data staff berhasil dihapus');
        }else {
            return redirect()->back()->with('error', 'Data staff gagal dihapus');
        }
    }
    public function signUp(Request $request)
    {
        //class untuk mengambil data dari form
        $request->validate(
            [
                // wajib diisi min/minimal karakter
                'first_name' => 'required|min:3',
                'last_name' => 'required|min:3',
                // dns emailnya valid,@gmail.com,@company.com, dll
                'email' => 'required|email:dns',
                'password' => 'required|min:8',
            ],
            [
                'first_name.required' => 'Nama depan wajib diisi',
                'first_name.min' => 'Nama depan wajib diisi minimal 3 Huruf',
                'last_name.required' => 'Nama belakang wajib diisi',
                'last_name.min' => 'Nama belakang wajib diisi minimal 3 Huruf',
                'email.required' => 'Email wajib diisi',
                'email.email' => 'Email wajib diisi dengan data yang valid',
                'password.required' => 'Password wajib diisi',
                'password.min' => 'Password minimal 8 Huruf',
            ]
        );
        $CreateUser = User::create([
            // nama_collum => $request->nama_input
            'name' => $request->first_name . ' ' . $request->last_name,
            'email' => $request->email,
            // hash:enskripsi data (mengubah menjadi karakter acak) agar tdk ada yh bisa menebak isinya)
            'password' => Hash::make($request->password),
            // pengguna idk bisa  memmilih role akses jd manual do tambahkan user
            'role' => 'user',
        ]);
        if ($CreateUser) {
            // redirect memindahkan halaman , route():name routing yg di tuju
            // with mengirimkan session biasanya untuk modifikasi
            return redirect()->route('login')->with('success', 'silahkan login');
        } else {
            // back kembali ke halaman sebelumnya
            return redirect()->back()->with('error', 'gagal!!!!!!, silahkan coba lagi');
        }

    }
    public function loginAuth(Request $request)
        {
            $request->validate([
                'email'=> 'required',
                'password'=> 'required'
            ], [
                'email.required'=> 'Email wajib diisi',
                'password.required'=> 'Password harus diisi'
            ]);
            //mengambil data yg akan di verifikasi
            $data = $request->only('email', 'password');
            // auth :: ->clas laravel untuk penanganan autentikasi
            // attempt -> class untuk  mencocokan email-pw atau username-pw kalau cocok akan di simpan datanya ke session auth
            if(Auth::attempt($data)){
                if (Auth::user()->role == 'admin') {
                    return redirect()->route('admin.dashboard')->with('succes, Berhasil login.');
                } elseif(Auth::user()->role == 'staff'){
                    return redirect()->route('staff.dashboard')->with('success', 'Berhasil login');
                } else 
                return redirect()->route('home')->with('success', 'Berhasil login');
                } else {
                    return redirect()->back()->with('error', 'Gagal login, Gagal login! pastikan email dan password sesuai');
                }

        }
                public function logout() {
                Auth::logout();
                return redirect()->route('home')->with('logout', 'Berhasil logout! silahkan login kembali untuk akses lengkap');
            }

    public function home()
    {
        // mengurutkan -> orderby('field', 'asc/desc' : ASC(a-z 0-9 terbaru ke terlama),  DESC(z-a, 9-0 terlama ke terbaru))
        $users = User::where()->orderby('role', 'ASC')->get();
        return view('home', compact('users'));
    }

    public function export()
    {
        // nama file yang akan di unduh
        $fileName = 'data-staff.xlsx';
        // proses unduh
        return excel::download(new UserExport, $fileName);
    }

    public function trash()
    {

        // ORM yang digunakan terkait softdeletes
        // onlyTrashed() -> filter data yag sudah dihapus, delete_at BUKAN NULL
        // restore() -> mengembalikan data yang sudah dihapus (menghapus nilai tanggal pada deleted_at)
        // forceDelete() -> menghapus data secara permanen, data dihilangkan bahkan dari databasenya

        $userTrash = User::onlyTrashed()->get();
        return view('admin.users.trash', compact('userTrash'));
    }

    public function restore($id)
    {
        $user = User::onlyTrashed()->findOrFail($id);
        $user->restore();
        return redirect()->route('admin.users.index')->with('success', 'Pengguna berhasil dikembalikan!');
    }

    public function deletePermanent($id)
    {
        $user = User::onlyTrashed()->findOrFail($id);
        $user->forceDelete();
        return redirect()->back()->with('success', 'Pengguna berhasil dihapus permanen!');  
    }

    public function datatables()
    {
        $users = User::query();

        return DataTables::of($users)
            ->addIndexColumn()
            ->addColumn('role_badge', function ($user) {
                if ($user->role == 'admin') {
                    return '<span class="badge bg-danger">Admin</span>';
                } elseif ($user->role == 'staff') {
                    return '<span class="badge bg-primary">Staff</span>';
                } else {
                    return '<span class="badge bg-secondary">User</span>';
                }
            })
            ->addColumn('action', function ($user) {
                $actions = '';

                $actions .= '<a href="' . route('admin.users.edit', $user->id) . '" 
                        class="btn btn-warning btn-sm mx-2">Edit</a>';

                $actions .= '<form action="' . route('admin.users.destroy', $user->id) . '" method="POST" class="d-inline">
                        ' . csrf_field() . '' . method_field('DELETE') . '<button type="submit" class="btn btn-danger btn-sm">Hapus</button> </form>';
                return $actions;
            })
            ->rawColumns(['action', 'role_badge'])
            ->make(true);
    }
}