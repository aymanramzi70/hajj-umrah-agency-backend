<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'address',
        'phone_number',
        'email',
        'status',
    ];

    /**
     * Get the users (employees) for the branch.
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get the customers registered through this branch.
     */
    public function customers()
    {
        return $this->hasMany(Customer::class, 'source_branch_id');
    }
}
