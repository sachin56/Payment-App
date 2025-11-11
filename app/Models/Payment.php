<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'customer_id',
        'reference_no',
        'amount',
        'currency',
        'amount_usd',
        'payment_date',
        'status',
        'file_name',
    ];

    protected $casts = [
        'amount' => 'float',
        'amount_usd' => 'float',
        'payment_date' => 'datetime',
    ];

public function invoices()
{
    return $this->belongsToMany(
        Invoices::class,
        'invoice_payments',
        'payment_id',   // foreign key on pivot for Payment
        'invoice_id'    // foreign key on pivot for Invoice
    )->using(InvoicePayment::class)
     ->withTimestamps()
     ->withPivot('deleted_at');
}


    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

}
