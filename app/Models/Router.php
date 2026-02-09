<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Router extends Model
{
    protected $fillable = ['user_id', 'name', 'vpn_ip', 'local_ip', 'api_user', 'api_password'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function vouchers()
    {
        return $this->hasMany(Voucher::class);
    }
}
