<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Parents extends Model
{
    use HasFactory;

    protected $table = 'parents'; // Nama tabel di database

    protected $fillable = [
        'user_id',
        'id_parent',
        'name',
        'email',
        'phone',
        'jobs',
        'address',
        'is_father',
        'is_mother',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id'); // Relasi ke tabel users
    }
}
