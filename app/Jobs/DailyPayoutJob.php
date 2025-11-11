<?php

namespace App\Jobs;

use Exception;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Customer;
use App\Models\Invoices;
use App\Mail\InvoiceMail;
use Illuminate\Support\Str;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class DailyPayoutJob implements ShouldQueue
{
   use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        // Fetch unprocessed payments for today
        $payments = Payment::where('status', 'unprocessed')
            ->whereDate('payment_date', now()->toDateString())
            ->orderBy('customer_id')
            ->get()
            ->groupBy('customer_id');

        if ($payments->isEmpty()) {
            Log::info('No unprocessed payments found for today.');
            return;
        }

        foreach ($payments as $customerId => $records) {

            $customer = Customer::find($customerId);

            if (!$customer || empty($customer->email)) {
                Log::warning("Customer missing or email not found: ID = {$customerId}");
                continue;
            }

            // Calculate total
            $totalUSD = $records->sum('amount_usd');

            try{

                DB::beginTransaction();

                $invoice = new Invoices();
                $invoice->customer_id = $customer->id;
                $invoice->invoice_date = now();
                $invoice->total_amount_usd = $totalUSD;
                $invoice->invoice_number = 'INV-' . now()->format('Ymd') . '-' . Str::upper(Str::random(6));
                $invoice->save();

                // Attach payments to invoice pivot
                $invoice->payments()->attach($records->pluck('id'));

                // Update payment status
                Payment::whereIn('id', $records->pluck('id'))->update(['status' => 'processed']);

                DB::commit();

                Log::info("📄 Invoice Created: {$invoice->invoice_number}");

                // Send Invoice Email
                Mail::to($customer->email)->send(new InvoiceMail($invoice, $records));

            }catch(Exception $e){
                DB::rollBack();
                Log::error("Invoice processing failed for customer {$customerId}: {$e->getMessage()}"); 
            }

        }

    }
}
