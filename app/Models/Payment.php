<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'booking_id',
        'amount',
        'payment_date',
        'payment_method',
        'transaction_id',
        'received_by_user_id',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'amount' => 'decimal:2',
        'payment_date' => 'date',
    ];

    /**
     * Get the booking that the payment belongs to.
     */
    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    /**
     * Get the user (employee) who received the payment.
     */
    public function receivedByUser()
    {
        return $this->belongsTo(User::class, 'received_by_user_id');
    }
}
