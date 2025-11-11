<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoices extends Model
{
    use SoftDeletes;
    protected $fillable =[
        'customer_id',
        'invoice_number',
        'total_amount_usd',
        'invoice_date',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

public function payments()
{
    return $this->belongsToMany(Payment::class, 'invoice_payments', 'invoice_id', 'payment_id');
}
}
