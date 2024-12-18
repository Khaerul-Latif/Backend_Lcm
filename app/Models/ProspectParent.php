<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProspectParent extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'source',
        'id_city',
        'id_program',
        'invoice_sp',
        'call',
        'tgl_checkin',
        'invitional_code',
    ];

    public function city()
    {
        return $this->belongsTo(City::class, 'id_city');
    }

    public function program()
    {
        return $this->belongsTo(Program::class, 'id_program');
    }

    public function paymentSp()
    {
        return $this->belongsTo(Payment_sps::class, 'invoice_sp');
    }
}
