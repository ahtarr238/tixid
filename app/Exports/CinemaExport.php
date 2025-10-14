<?php

namespace App\Exports;

use App\Models\Cinema;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
// class untuk membuat td pada tabel excel
use Maatwebsite\Excel\Concerns\WithMapping;

class CinemaExport implements FromCollection, WithMapping,WithHeadings
{
    private $key = 0;
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Cinema::all();
    }
    
    public function headings(): array {
        return ['No', 'Judul', 'Lokasi'];
    }

    public function map($cinema): array {
        return [
            ++$this->key,
            $cinema->name,
            $cinema->location
        ];
    }
}