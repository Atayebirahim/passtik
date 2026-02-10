<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'plan',
        'voucher_limit',
        'is_admin',
    ];

    /**
     * Set the plan attribute with validation.
     */
    public function setPlanAttribute($value)
    {
        $allowedPlans = ['free', 'pro', 'enterprise'];
        $this->attributes['plan'] = in_array($value, $allowedPlans) ? $value : 'free';
    }

    /**
     * Set the voucher limit attribute with validation.
     */
    public function setVoucherLimitAttribute($value)
    {
        $this->attributes['voucher_limit'] = max(0, min((int)$value, 999999));
    }

    /**
     * The attributes that should be guarded from mass assignment.
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            if (!isset($user->plan)) {
                $user->plan = 'free';
            }
            if (!isset($user->voucher_limit)) {
                $user->voucher_limit = 50;
            }
            if (!isset($user->is_admin)) {
                $user->is_admin = false;
            }
        });
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     * 
     * Note: 'password' => 'hashed' uses Laravel's secure bcrypt hashing.
     * No plaintext passwords are stored.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
        ];
    }

    public function routers()
    {
        return $this->hasMany(Router::class);
    }

    public function vouchers()
    {
        return $this->hasManyThrough(Voucher::class, Router::class);
    }
    
    public function subscriptionRequests()
    {
        return $this->hasMany(SubscriptionRequest::class);
    }
    
    /**
     * Check if user can create more vouchers
     */
    public function canCreateVouchers($quantity = 1)
    {
        $currentCount = $this->vouchers()->count();
        return ($currentCount + $quantity) <= $this->voucher_limit;
    }
    
    /**
     * Get remaining voucher quota
     */
    public function getRemainingVoucherQuota()
    {
        return max(0, $this->voucher_limit - $this->vouchers()->count());
    }
}
