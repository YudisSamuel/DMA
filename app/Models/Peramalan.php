<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Peramalan extends Model
{
    protected $table = 'tb_Peramalan';
    public $timestamps = false;

    protected $fillable = [
        'Bulan',
        'Type_Produk',
        'Prediksi',
        'mape',
        'is_deleted', // Pastikan is_deleted ada dalam fillable
    ];



    // Jika ID adalah auto increment, tidak perlu diubah
    protected $primaryKey = 'id';
}
