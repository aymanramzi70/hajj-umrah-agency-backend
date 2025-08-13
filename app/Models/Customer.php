<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone_number',
        'national_id',
        'passport_number',
        'date_of_birth',
        'gender',
        'address',
        'source_branch_id',
        'user_id',
        'added_by_user_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'date_of_birth' => 'date',
    ];

    /**
     * Get the branch that registered the customer.
     */
    public function sourceBranch()
    {
        return $this->belongsTo(Branch::class, 'source_branch_id');
    }

    /**
     * Get the bookings for the customer.
     */
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function addedBy()
    {
        return $this->belongsTo(User::class, 'added_by_user_id');
    }
    
}
