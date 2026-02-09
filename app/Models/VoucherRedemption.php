<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VoucherRedemption extends Model
{
    protected $fillable = [
        'voucher_id', 'ip_address', 'mac_address', 'user_agent',
        'device_type', 'status', 'error_message', 'metadata'
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function voucher()
    {
        return $this->belongsTo(Voucher::class);
    }
}
