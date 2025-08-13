<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone_number',
        'password',
        'branch_id',
        'role',
        'fcm_token',
        'status',
    ];

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
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    /**
     * Get the branch that the user belongs to.
     */
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * Get the bookings made by the user.
     */
    public function bookings()
    {
        return $this->hasMany(Booking::class, 'booked_by_user_id');
    }

    /**
     * Get the payments received by the user.
     */
    public function paymentsReceived()
    {
        return $this->hasMany(Payment::class, 'received_by_user_id');
    }
    public function customer()
    {
        return $this->hasOne(Customer::class);
    }
    public function customers()
    {
        return $this->hasMany(Customer::class, 'added_by_user_id');
    }
}
