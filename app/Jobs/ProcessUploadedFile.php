<?php

namespace App\Jobs;

use League\Csv\Reader;
use App\Models\Payment;
use App\Models\Customer;
use League\Csv\Statement;
use App\Models\PaymentLog;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\ExchangeRateService;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ProcessUploadedFile implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $fileKey;
    public $uploadedBy;

    public function __construct(string $fileKey, $uploadedBy = null)
    {
        $this->fileKey = $fileKey;
        $this->uploadedBy = $uploadedBy;
    }

    public function handle(ExchangeRateService $exchangeRateService)
    {
        if (!$this->fileKey) {
            Log::error("ProcessUploadedFile failed: fileKey is null");
            return;
        }

        try {
            $stream = Storage::disk('s3')->readStream($this->fileKey);
            if (! $stream) {
                Log::error("ProcessUploadedFile: cannot read stream for {$this->fileKey}");
                return;
            }

            $csv = Reader::createFromStream($stream);
            $csv->setHeaderOffset(0);

            // Normalize headers
            $rawHeaders = $csv->getHeader();
            $normalized = array_map(fn($h) => strtolower(trim($h)), $rawHeaders);
            $stmt = (new Statement());
            $records = $stmt->process($csv);

            $ratesPayload = $exchangeRateService->getLatestRates('USD');

            $rowNum = 1; // header row
            foreach ($records as $row) {
                $rowNum++;
                $row = array_change_key_case($row, CASE_LOWER);
                $this->processRow($row, $rowNum, $ratesPayload);
            }

            if (is_resource($stream)) {
                fclose($stream);
            }
        } catch (\Throwable $e) {
            Log::error('ProcessUploadedFile failed: '.$e->getMessage(), [
                'file' => $this->fileKey,
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }

    protected function processRow(array $row, int $rowNum, array $rates)
    {
        $row = array_map(fn($v) => is_string($v) ? trim($v) : $v, $row);

        $required = ['customer_email', 'amount', 'currency', 'reference_no'];

        foreach ($required as $k) {
            if (! isset($row[$k]) || $row[$k] === '' || $row[$k] === null) {
                $this->logFailure(null, $row['reference_no'] ?? null, "Missing required column: {$k}", $row, $rowNum);
                return;
            }
        }

        if (! filter_var($row['customer_email'], FILTER_VALIDATE_EMAIL)) {
            $this->logFailure(null, $row['reference_no'] ?? null, "Invalid email: {$row['customer_email']}", $row, $rowNum);
            return;
        }

        if (! is_numeric($row['amount'])) {
            $this->logFailure(null, $row['reference_no'] ?? null, "Invalid amount: {$row['amount']}", $row, $rowNum);
            return;
        }

        $currency = strtoupper($row['currency']);
        $amount = (float) $row['amount'];

        $paymentDate = now()->toDateTimeString();
        if (!empty($row['date_time'])) {
            $candidate = trim($row['date_time']);
            if (strlen($candidate) >= 19 && preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/', substr($candidate, 0, 19))) {
                $paymentDate = substr($candidate, 0, 19);
            } elseif (preg_match('/^\d{4}-\d{2}-\d{2}$/', $candidate)) {
                $paymentDate = $candidate;
            }
        }

        $rate = $rates['rates'][$currency] ?? null;
        if (!$rate || $rate == 0) {
            $this->logFailure(null, $row['reference_no'] ?? null, "Missing or zero exchange rate for {$currency}", $row, $rowNum);
            return;
        }

        $amountUsd = round($amount / $rate, 6);

        $existing = Payment::where('reference_no', $row['reference_no'])->first();
        if ($existing) {
            $this->logFailure($existing->id, $row['reference_no'], 'Duplicate reference_no', $row, $rowNum);
            return;
        }

        $customer = Customer::firstOrCreate(
            ['customer_id' => $row['customer_id'] ?? null],
            ['name' => $row['customer_name'] ?? null, 'email' => $row['customer_email']]
        );

        try {

            DB::beginTransaction();

            $payment = new Payment();
            $payment->customer_id = $customer->id;
            $payment->reference_no = $row['reference_no'];
            $payment->amount = $amount;
            $payment->currency = $currency;
            $payment->amount_usd = $amountUsd;
            $payment->payment_date = $paymentDate;
            $payment->status = 'unprocessed';
            $payment->file_name = $this->fileKey;
            $payment->save();

            $paymentLog = new PaymentLog();
            $paymentLog->payment_id = $payment->id;
            $paymentLog->reference_no = $payment->reference_no;
            $paymentLog->status = 'success';
            $paymentLog->message = "Imported successfully (row {$rowNum})";
            $paymentLog->save();

            DB::commit();

        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('ProcessUploadedFile: create payment exception', [
                'error' => $e->getMessage(),
                'row' => $row,
            ]);


            $paymentLog = new PaymentLog();
            $paymentLog->payment_id = $payment->id;
            $paymentLog->reference_no = $row['reference_no'] ?? null;
            $paymentLog->status = 'failed';
            $paymentLog->message = "DB insert exception: " . $e->getMessage() . " | Row: " . json_encode($row);
            $paymentLog->save();
        }
    }

    protected function logFailure($paymentId = null, $reference = null, $message = '', $row = [], $rowNum = null)
    {
        PaymentLog::create([
            'payment_id' => $paymentId,
            'reference_no' => $reference,
            'status' => 'failed',
            'message' => $message . ($rowNum ? " (row {$rowNum})" : '') . " | Row: " . json_encode($row),
        ]);

        Log::warning('ProcessUploadedFile row failed', [
            'file' => $this->fileKey,
            'row' => $row,
            'message' => $message,
            'row_number' => $rowNum,
        ]);
    }
}
