<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CheckHistory extends Model
{
    protected $fillable = [
        'nolang',
        'status',
        'nama_pelanggan',
        'total_tagihan',
        'ip_address',
        'user_agent',
    ];
}
