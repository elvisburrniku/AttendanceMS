<?php

namespace App\Models; 
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;


    public function employee()
    {
        return $this->hasOne('App\Models\Employee', 'nickname', 'email');
    }

    public function getRouteKeyName()
    {
        return 'name';
    }
    
    public function roles()
    {
        return $this->belongsToMany('App\Models\Role', 'role_users', 'user_id', 'role_id');
    }

    public function hasAnyRole($roles)
    {
        if (is_array($roles)) {
            foreach ($roles as $role) {
                if ($this->hasRole($role)) {
                    return true;
                }
            }
        } else {
            if ($this->hasRole($roles)) {
                return true;
            }
        }

        return false;
    }

    public function hasRole($role)
    {
        $userRole = $this->roles()->first();
        if ($userRole && $userRole->slug === $role) {
            return true;
        }
        return false;
    }


    protected $fillable = [
        'name', 'email', 'password', 'pin_code', 'email_verified_at', 
        'trial_ends_at', 'subscription_status', 'subscription_ends_at', 
        'stripe_customer_id', 'role'
    ];

  
    protected $hidden = [
        'pin_code','password', 'remember_token',
    ];

  
    protected $casts = [
        'email_verified_at' => 'datetime',
        'trial_ends_at' => 'datetime',
        'subscription_ends_at' => 'datetime',
    ];
}
