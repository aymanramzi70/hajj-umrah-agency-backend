<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Agent extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'company_name',
        'contact_person',
        'email',
        'phone_number',
        'address',
        'license_number',
        'commission_rate',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'commission_rate' => 'decimal:2', // ensure it's treated as a decimal with 2 places
    ];

    /**
     * Get the bookings made by the agent.
     */
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}
