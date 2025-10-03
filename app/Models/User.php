<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'middle_name',
        'last_name',
        'designation',
        'office',
        'employee_id',
        'email',
        'password',
        'role',
        'twofactor_code',
        'twofactor_code_expires_at',
        'email_verified_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Regenerate the two-factor authentication code.
     */
    public function regenerateTwoFactorCode()
    {
        $this->timestamps = false;
        $this->twofactor_code = rand(100000, 999999);
        $this->twofactor_code_expires_at = now()->addMinutes(10);
        $this->save();
    }

    public function clearTwoFactorCode()
    {
        $this->timestamps = false;
        $this->twofactor_code = null;
        $this->twofactor_code_expires_at = null;
        $this->save();
    }

    /**
     * Check if user is active
     */
    public function isActive()
    {
        return !is_null($this->email_verified_at);
    }

    /**
     * Get user status
     */
    public function getStatusAttribute()
    {
        return $this->isActive() ? 'Active' : 'Inactive';
    }

    public function purchaseRequests()
    {
        return $this->hasMany(PurchaseRequest::class);
    }

    public function uploadedDocuments()
    {
        return $this->hasMany(UploadedDocument::class);
    }

    public function activities()
    {
        return $this->hasMany(UserActivity::class);
    }
}
