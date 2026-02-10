<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Router extends Model
{
    protected $fillable = ['user_id', 'name', 'vpn_ip', 'local_ip', 'api_user', 'api_password', 'vpn_public_key', 'vpn_private_key', 'status'];

    protected $hidden = ['api_password', 'vpn_private_key'];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function vouchers()
    {
        return $this->hasMany(Voucher::class);
    }
}
