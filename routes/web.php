<?php

use App\Http\Controllers\CinemaController;
use App\Http\Controllers\MovieController;
use App\Http\Controllers\PromoController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', [MovieController::class, 'home'])->name('home');

// semua data film home
Route::get('/home/movies', [MovieController::class, 'homeAllMovies'])->name('home.movies');

// Route::get('/schedules', function () {
//     return view('schedule/'{movie_id}, [MovieController::class, 'movieSchedule']);
// })->name('schedules.detail');

Route::get('/schedules/{movie_id}', [MovieController::class, 'movieSchedules'])->name('schedule.detail');


Route::middleware('isUser')->group(function() {
    Route::get('/schedules/{scheduleId}/hours/{hourId}/show-seats', [TicketController::class, 'showSeats'])->name('schedules.seats');

    Route::prefix('/tickets')->name('tickets.')->group(function() {
        Route::post('/', [TicketController::class, 'store'])->name('store');
        Route::get('/{ticketId}/order', [TicketController::class, 'ticketOrder'])->name('order');
    });
});


// menu "bioskop" pada navbar user ( penggina umum)

Route::get('/cinema/list', [CinemaController::class, 'cinemaList'])->name('cinemas.list');
Route::get('/cinemas/{cinema_id}/schedules', [CinemaController::class, 'cinemaSchedules'])->name('cinemas.schedules');

Route::middleware('isGuest')->group(function () {
    Route::get('/login', function () {
        return view('login');
    })->name('login');

    Route::post('/login', [UserController::class, 'loginAuth'])->name('login.auth');

    Route::get('/sign-up', function () {
        return view('signup');
    })->name('sign_up');

    Route::post('/sign-up', [UserController::class, 'signUp'])->name('sign_up.send');
});



//httpmethod route::
//1. get -> menampilkan halaman
//2. post-> mengambil/menambahkan data
//3. patch/put -> mengubah data
//4. delete -> menghapus data


Route::get('/logout', [UserController::class, 'logout'])->name('logout');

//prefix(): awalan, menulis/admin satu kali untuk 16 route crud
//middleware('isAdmin) memanggil middleware yang akan di gunakan
//middelware : Authorization, pengaturan hak akses pengguna

Route::middleware('isAdmin')->prefix('/admin')->name('admin.')->group(function () {
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('dashboard');

    Route::prefix('/cinemas')->name('cinemas.')->group(function () {
        Route::get('/', [CinemaController::class, 'index'])->name('index');
        Route::get('create', function () {
            return view('admin.cinema.create');
        })->name('create');
        Route::post('/store', [CinemaController::class, 'store'])->name('store');
        Route::get('edit/{id}', [CinemaController::class, 'edit'])->name('edit');
        //id berfungsi untuk memberi parameter id mana yang ingin di edit
        Route::put('update/{id}', [CinemaController::class, 'update'])->name('update');
        Route::delete('destroy/{id}', [CinemaController::class, 'destroy'])->name('delete');
        Route::get('/export', [Cinemacontroller::class, 'export'])->name('export');        
        // Recycle Bin Routes
        Route::get('/trash', [CinemaController::class, 'trash'])->name('trash');
        Route::patch('/restore/{id}', [CinemaController::class, 'restore'])->name('restore');
        Route::delete('/delete-permanent/{id}', [CinemaController::class, 'deletePermanent'])->name('delete_permanent');
        Route::patch('/restore-all', [CinemaController::class, 'restoreAll'])->name('restore_all');
        Route::delete('/delete-all-permanent', [CinemaController::class, 'deleteAllPermanent'])->name('delete_all_permanent');
        Route::get('/datatables', [Cinemacontroller::class, 'datatables'])->name('datatables');
    });

    Route::prefix('/users')->name('users.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::get('/create', function () {
            return view('admin.users.create');
        })->name('create');
        Route::post('/store', [UserController::class, 'store'])->name('store');
        Route::get('/edit/{id}', [UserController::class, 'edit'])->name('edit');
        Route::put('/update/{id}', [UserController::class, 'update'])->name('update');
        Route::delete('/delete/{id}', [UserController::class, 'destroy'])->name('destroy');
        Route::get('/export', [UserController::class, 'export'])->name('export');
        Route::get('/trash', [UserController::class, 'trash'])->name('trash');
        Route::patch('/restore/{id}', [UserController::class, 'restore'])->name('restore');
        Route::delete('/delete-permanent/{id}', [UserController::class, 'deletePermanent'])->name('delete_permanent');
        Route::get('/datatables', [UserController::class, 'datatables'])->name('datatables');
    });

    Route::prefix('/movies')->name('movies.')->group(function() {
        Route::get('/', [MovieController::class, 'index'])->name('index');
        Route::get('/create', [MovieController::class, 'create'])->name('create');
        Route::post('/store', [MovieController::class, 'store'])->name('store');
        Route::get('/edit/{id}', [MovieController::class, 'edit'])->name('edit');
        Route::put('/update/{id}', [MovieController::class, 'update'])->name('update');
        Route::patch('/actived/{id}', [MovieController::class, 'actived'])->name('actived');
        Route::delete('/delete/{id}', [MovieController::class, 'destroy'])->name('destroy');
        Route::get('/export', [MovieController::class, 'export'])->name('export');
        Route::get('/trash', [MovieController::class, 'trash'])->name('trash');
        Route::patch('/restore/{id}', [MovieController::class, 'restore'])->name('restore');
        Route::delete('/delete-permanent/{id}', [MovieController::class, 'deletePermanent'])->name('delete_permanent');
        Route::get('/datatables', [MovieController::class, 'datatables'])->name('datatables');
    });
    
});

// middleware staff
Route::middleware('isStaff')->prefix('/staff')->name('staff.')->group(function () {
    Route::get('/dashboard', function () {
        return view('staff.dashboard');
    })->name('dashboard');

    Route::prefix('/promo')->name('promos.')->group(function () {
        Route::get('/', [PromoController::class, 'index'])->name('index');
        Route::get('/create', [PromoController::class, 'create'])->name('create');
        Route::post('/store', [PromoController::class, 'store'])->name('store');
        Route::get('/edit/{id}', [PromoController::class, 'edit'])->name('edit');
        Route::put('/update/{id}', [PromoController::class, 'update'])->name('update');
        Route::patch('/actived/{id}', [PromoController::class, 'actived'])->name('actived');
        Route::delete('/delete/{id}', [PromoController::class, 'destroy'])->name('delete');
        Route::get('/export', [PromoController::class, 'export'])->name('export');
        Route::get('/trash', [PromoController::class, 'trash'])->name('trash');
        Route::patch('/restore/{id}', [PromoController::class, 'restore'])->name('restore');
        Route::delete('/delete-permanent/{id}', [PromoController::class, 'deletePermanent'])->name('delete_permanent');
        Route::get('/datatables', [PromoController::class, 'datatables'])->name('datatables');
    });

    Route::prefix('/schedules')->name('schedules.')->group(function(){
        Route::get('/', [ScheduleController::class , 'index'])->name('index');
        Route::post('/store', [ScheduleController::class, 'store'])->name('store');
        Route::get('/edit/{id}', [ScheduleController::class, 'edit'])->name('edit');
        Route::patch('/update/{id}', [ScheduleController::class, 'update'])->name('update');
        Route::delete('/delete/{id}', [ScheduleController::class, 'destroy'])->name('delete');
        Route::get('/trash',[ScheduleController::class, 'trash'])->name('trash');
        Route::patch('/restore/{id}', [ScheduleController::class, 'restore'])->name('restore');
        Route::delete('/delete-permanent/{id}', [ScheduleController::class, 'deletePermanent'])->name('delete_permanent');
        Route::get('/datatables', [ScheduleController::class, 'datatables'])->name('datatables');
    });

});
