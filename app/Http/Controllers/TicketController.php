<?php

namespace App\Http\Controllers;

use App\Models\Promo;
use App\Models\Ticket;
use App\Models\Schedule;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function showSeats($scheduleId, $hourId) {
        // dd($scheduleId, $hourId);
        $schedule = Schedule::where('id', $scheduleId)->with('cinema')->first();
        // jika tidak ada data jam kasi default nilai kosong
        $hour = $schedule['hours'][$hourId] ?? '-';
        return view('schedule.show-seats', compact('schedule', 'hour'));
    }

    public function index()
    {
        //
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
            'user_id' => 'required',
            'schedule_id' => 'required',
            'hours' => 'required',
            'total_price' => 'required',
            'quantity' => 'required',
            'rows_of_seats' => 'required',
        ]);

        $createData = Ticket::create([
            'user_id' => $request->user_id,
            'schedule_id' => $request->schedule_id,
            'hours' => $request->hours,
            'total_price' => $request->total_price,
            'quantity' => $request->quantity,
            'rows_of_seats' => $request->rows_of_seats,
            'actived' => 0, // kalau uda di bayar baru diubah ke 1 
            'date' => now(),
        ]);

        // karena dipanggil di ajax return nya dalam bentuk json
        return response()->json([
            'message' => 'Berhasil membuat data tiket',
            'data' => $createData
        ]);
    }

    public function ticketOrder($ticketId) {
        $ticket = Ticket::where('id', $ticketId)->with('schedule.movie', 'schedule.cinema')->first();
        $promos = Promo::where('actived', 1)->get();
        return view('schedule.order', compact('ticket', 'promos'));
    }


    /**     * Display the specified resource.
     */
    public function show(Ticket $ticket)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Ticket $ticket)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Ticket $ticket)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Ticket $ticket)
    {
        //
    }
}
