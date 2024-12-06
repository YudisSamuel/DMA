<?php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable
{
    use Notifiable;

    protected $table = 'tb_pengguna';
    protected $primaryKey = 'id_pgn';

    protected $fillable = [
        'username',
        'password',
        'profile_photo'
    ];

    protected $hidden = [
        'password',
    ];

    // Tambahkan accessor untuk URL foto profil
    public function getProfilePhotoUrlAttribute()
    {
        if ($this->profile_photo) {
            return asset('storage/profile_photos/' . $this->profile_photo);
        }

        // Foto default jika tidak ada
        return asset('path/to/default/profile-image.png');
    }
}
