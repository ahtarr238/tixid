<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Cinema extends Model
{
    //daftar softdeletes
    use SoftDeletes;
    protected $fillable = ['name', 'location'];

    // fungsi relasi, karena one to ma, namanya jamak
    public function schedules(){
        //definisi jenis relasi (one to one / one to many)
        return $this->HasMany(Schedule::class);
    }
}
