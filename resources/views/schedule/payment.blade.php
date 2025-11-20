@extends('templates.app')
@section('content')
    <div class="card w-50 d-block mx-auto my-5 p-4">
        <div class="card-body">
            <h5>Selesaikan Pembayaran</h5>

            <img src="{{ asset('storage/' . $ticket['ticketPayment']['barcode']) }}" class="d-block mx-auto">
            <div class="d-flex justify-content-between">
                <p>{{ $ticket['quantity'] }} Tiket
                </p>
            </div>

            <div class="d-flex justify-content-between">
                <p>Harga Tiket</p>
                <p><b>Rp. {{ number_format($ticket['schedule']['price'], 0, ',', '.') }}
                        <span class="text-secondary"> x {{ $ticket['quantity'] }}</span></b>
                </p>
            </div>

            <div class="d-flex justify-content-between">
                <p>Biaya Layanan</p>
                <p><b>Rp. 5.000
                        <span class="text-secondary"> x {{ $ticket['quantity'] }}</span></b>
                </p>
            </div>

            <div class="d-flex justify-content-between">
                <p>Promo</p>
                @php
                    $base = $ticket['schedule']['price'] * $ticket['quantity'];

                    if ($ticket['promo_id'] != null) {
                        if ($ticket['promo']['type'] === 'percent') {
                            $discount = $base * ($ticket['promo']['discount'] / 100);
                        } else {
                            $discount = $ticket['promo']['discount'];
                        }
                    } else {
                        $discount = 0;
                    }

                    $fee = 5000 * $ticket['quantity'];
                    $total = $base - $discount + $fee;
                @endphp

            </div>

            <hr>

            @php

                $price = $ticket['total_price'] + 4000 * $ticket['quantity'];
            @endphp

            <div class="d-flex justify-content-end">
                <p><b>Rp. {{ number_format($total, 0, ',', '.') }}</b></p>
            </div>

            <form action="{{route('tickets.payment.proof', $ticket['id'])}}" method="POST">
                @csrf
                @method('PATCH')
                <button type="submit" class="btn btn-primary btn-lg btn-block">Sudah Dibayar</button>
            </form>
        </div>
    </div>
@endsection
