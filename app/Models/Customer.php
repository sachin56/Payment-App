<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    protected $fillable = [
        'customer_id',
        'name',
        'email',
    ];


    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }
}
