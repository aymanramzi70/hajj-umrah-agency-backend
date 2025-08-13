<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'booking_code',
        'package_id',
        'customer_id',
        'agent_id',
        'booked_by_user_id',
        'number_of_people',
        'total_price',
        'paid_amount',
        'remaining_amount',
        'payment_status',
        'booking_status',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'total_price' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'remaining_amount' => 'decimal:2',
    ];

    /**
     * Get the package that the booking belongs to.
     */
    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    /**
     * Get the customer that owns the booking.
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get the agent that owns the booking.
     */
    public function agent()
    {
        return $this->belongsTo(Agent::class);
    }

    /**
     * Get the user (employee) who made the booking.
     */
    public function bookedByUser()
    {
        return $this->belongsTo(User::class, 'booked_by_user_id');
    }

    /**
     * Get the payments for the booking.
     */
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}
