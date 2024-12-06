<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DataProduct extends Model
{
    use HasFactory;

    protected $table = 'tb_DataProduct'; // Nama tabel
    protected $primaryKey = 'id'; // Menentukan kolom primary key
    public $incrementing = false; // Jika id_produk bukan auto-increment
    protected $keyType = 'int'; // Tipe data primary key
    protected $fillable = ['Tanggal', 'Kode_Produk', 'Type_Produk', 'Jumlah_Terjual', 'Harga_Produk']; // Tambahkan 'id_produk' jika diperlukan

}
