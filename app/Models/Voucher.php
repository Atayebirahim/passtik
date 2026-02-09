<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    protected $fillable = [
        'router_id', 'code', 'password', 'profile', 'duration', 'bandwidth',
        'status', 'expires_at', 'used_at', 'redeemed_at', 'redeemed_ip',
        'device_mac', 'device_info'
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'used_at' => 'datetime',
        'redeemed_at' => 'datetime',
    ];

    public function router()
    {
        return $this->belongsTo(Router::class);
    }

    public function redemptions()
    {
        return $this->hasMany(VoucherRedemption::class);
    }

    public function isExpired()
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function isRedeemable()
    {
        return $this->status === 'pending' && !$this->isExpired();
    }

    public function getDurationFormatted()
    {
        if ($this->duration < 60) {
            return $this->duration . ' minutes';
        }
        $hours = floor($this->duration / 60);
        $minutes = $this->duration % 60;
        return $hours . 'h' . ($minutes > 0 ? ' ' . $minutes . 'm' : '');
    }
}
