<?php

namespace App\Exports;

use App\Models\Movie;
use Maatwebsite\Excel\Concerns\FromCollection;
//class untuk membuat th pada table excel
use Maatwebsite\Excel\Concerns\WithHeadings;
// class untuk membuat td pada tabel excel
use Maatwebsite\Excel\Concerns\WithMapping;
//
use Carbon\Carbon;

class MovieExport implements FromCollection, WithHeadings, WithMapping
{

    private $key = 0;

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Movie::all();
    }

    //menentukan isi th
    public function headings(): array
    {
        return ['No', 'Judul' , 'Durasi', 'Genre', 'Sutradara', 'Usia Minimal', 'Poster' , ' Sinopsis', 'Status'];
    }

    //menentukan isi td
    public function map($movie): array
    {
        return [
            //gunakan this karena key merupakan property oop jadi dipanggil dengan this
            ++$this->key,
            $movie->title,
            //02:00 jadi 2 jam 00 menit
            // Format H ambil jam dari duration 
            Carbon::parse($movie->duration)->format('H') . "jam" . Carbon::parse($movie->duration)->format("i") . "Menit",
            $movie->genre,
            $movie->director,
            $movie->age_rating = "+",
            //poster berupa url public : asset()
            asset('storage') . "/" .  $movie->poster,
            $movie->description,
            // jika actived == 1 munculkan aktif, tidak non aktif
            $movie->actived == 1 ? 'Aktif' : 'Non-aktif'
        ];
    }
}
