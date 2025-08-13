<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'type',
        'start_date',
        'end_date',
        'price_per_person',
        'agent_price_per_person',
        'number_of_days',
        'available_seats',
        'status',
        'includes',
        'excludes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'price_per_person' => 'decimal:2',
        'agent_price_per_person' => 'decimal:2',
        'includes' => 'array', // Cast JSON column to array
        'excludes' => 'array', // Cast JSON column to array
    ];

    /**
     * Get the bookings for the package.
     */
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}
