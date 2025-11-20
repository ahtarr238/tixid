<?php

namespace App\Http\Controllers;

use App\Models\Promo;
use App\Models\Ticket;
use App\Models\Schedule;
use App\Models\TicketPayment;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class TicketController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function showSeats($scheduleId, $hourId)
    {
        // dd($scheduleId, $hourId);
        $schedule = Schedule::where('id', $scheduleId)->with('cinema')->first();
        // jika tidak ada data jam kasi default nilai kosong
        $hour = $schedule['hours'][$hourId] ?? '-';
        // ambil data dari tiket yang sesuai dengan jam, tanggal, dan sudah dibayar
        $seats = Ticket::whereHas('ticketPayment', function ($q) {
            // whereDate() mencari berdasarkan tanggal
            $q->whereDate('paid_date', now()->format('Y-m-d'));
        })->whereTime('hours', $hour)->pluck('rows_of_seats');
        // pluck() mengamvil hanya dari 1 field, bedanya dengan value() kalau value() ambil 1 data pertama dari 
        // field tersebut, kalau pluck() ambil semua data dari field tersebut
        $seatsFormat = array_merge(...$seats);
        // (...) spread operator mengeluarkan nilai array
        // spread operator mengeluarkan nilai array lalu disimpan lagi ke 1 dimensi
        // dd($seatsFormat);
        return view('schedule.show-seats', compact('schedule', 'hour', 'seatsFormat'));
    }

    public function chartData()
    {
        // ambil data dibulan ini
        $month = now()->format('m');
        // ambil data dari tiket yang sudah dibayar dan dbayar dibulan ini, kemudian di kelompokan datanya berdsarkantanggal pembayaran 
        $tickets = Ticket::whereHas('ticketPayment', function ($q) use ($month) {
            $q->whereMonth('paid_date', $month);
        })->get()->groupBy(function ($ticket) {
            return \Carbon\Carbon::parse($ticket->ticketPayment->paid_date)->format('Y-m-d');
        })->toArray();
        // $tickets berisi tanggal data di tanggal tersebut
        // pisahkan tanggal untuk labels di chartjs
        $labels = array_keys($tickets);
        // Hitung isi data di key tanggal tersebut untuk data di chratjs
        $data = [];
        foreach ($tickets as $item) {
            array_push($data, count($item));
        }
        return response()->json([
            'labels' => $labels,
            'data' => $data,
        ]);
    }

    public function index()
    {
        $ticketActive = Ticket::whereHas('ticketPayment', function ($q) {
            $q->whereDate('booked_date', now()->format('Y-m-d'))->where('paid_date', '<>', NULL);
        })->get();
        $ticketNonActive = Ticket::whereHas('ticketPayment', function ($q) {
            $q->whereDate('booked_date', '<', now()->format('Y-m-d'))->where('paid_date', '<>', NULL);
        })->get();
        return view('ticket.index', compact('ticketActive', 'ticketNonActive'));
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

    public function ticketOrder($ticketId)
    {
        $ticket = Ticket::where('id', $ticketId)->with('schedule.movie', 'schedule.cinema')->first();
        $promos = Promo::where('actived', 1)->get();
        return view('schedule.order', compact('ticket', 'promos'));
    }


    public function ticketPayment(Request $request)
    {
        $kodeBarcode = 'TICKET' . $request->ticket_id;

        $qrImage = QrCode::format('svg')->size(300)->margin(2)->errorCorrection('H')->generate($kodeBarcode);
        // penamaan file
        $filename = $kodeBarcode . '.svg';
        // tempat menyimpan barcode public/barcodes
        $path = 'barcodes/' . $filename;
        Storage::disk('public')->put($path, $qrImage);

        $createData = TicketPayment::create([
            'ticket_id' => $request->ticket_id,
            'barcode' => $path,
            'status' => 'process',
            'booked_date' => now()
        ]);

        $ticket = Ticket::find($request->ticket_id);
        if ($request->promo_id != NULL) {
            $promo = Promo::find($request->promo_id);
            if ($promo['type'] == 'percent') {
                $discount = $ticket['total_price'] * ($promo['discount'] / 100);
            } else {
                $discount = $promo['discount'];
            }
            $totalPrice = $ticket['total_price'] - $discount;
            // update total harga dengan setelah menggunakan diskon
            $updateTicket = Ticket::where('id', $request->ticket_id)->update([
                'promo_id' => $request->promo_id,
                'total_price' => $totalPrice
            ]);
        }


        return response()->json([
            'message' => 'Berhasil membuat pesanan tiket sementara!',
            'data' => $createData
        ]);
    }


    public function ticketPaymentPage($ticketId)
    {
        $ticket = Ticket::where('id', $ticketId)->with([
            'promo',
            'ticketPayment',
            'schedule'
        ])->first();
        return view('schedule.payment', compact('ticket'));
    }

    public function paymentProof($ticketId)
    {
        $updateData = Ticket::where('id', $ticketId)->update([
            'actived' => 1,
        ]);

        $updatePayment = TicketPayment::where('ticket_id', $ticketId)->update([
            'paid_date' => now()
        ]);

        return redirect()->route('tickets.receipt', $ticketId);
    }

    public function ticketReceipt($ticketId)
    {
        $ticket = Ticket::where('id', $ticketId)->with(['schedule', 'schedule.cinema', 'schedule.movie', 'ticketPayment'])
            ->first();
        return view('schedule.receipt', compact('ticket'));
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

    public function exportPdf($ticketId)
    {
        $ticket = Ticket::where('id', $ticketId)->with(['schedule', 'schedule.cinema', 'schedule.movie', 'promo', 'ticketPayment'])->first()->toArray();
        view()->share('ticket', $ticket);
        $pdf = Pdf::loadView('schedule.export-pdf', $ticket);
        $fileName = 'TICKET' . $ticket['id'] . '.pdf';
        return $pdf->download($fileName);
    }
}
