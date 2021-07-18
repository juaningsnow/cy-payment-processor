<?php

namespace App\Models;

use BaseCode\Common\Exceptions\GeneralApiException;
use BaseCode\Common\Traits\HasMany;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

// use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    // use HasProfilePhoto;
    use Notifiable;
    use HasMany;
    // use TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'is_admin'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        // 'two_factor_recovery_codes',
        // 'two_factor_secret',
    ];

    public function companies()
    {
        return $this->belongsToMany(Company::class, 'user_companies', 'user_id', 'company_id')->withPivot([
            'is_active'
        ])->withTimestamps();
    }
    
    public function userCompanies()
    {
        return $this->hasMany(UserCompany::class);
    }

    public function getActiveCompany()
    {
        return $this->userCompanies()->where('is_active', true)->first()->company;
    }

    public function isAdmin()
    {
        return (bool) $this->is_admin;
    }
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    // protected $casts = [
    //     'email_verified_at' => 'datetime',
    // ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    // protected $appends = [
    //     'profile_photo_url',
    // ];
}
