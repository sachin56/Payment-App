<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice - {{ $invoice->invoice_number }}</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f6f6f6;
            margin: 0; padding: 0;
        }
        .container {
            width: 600px;
            margin: 25px auto;
            background: #ffffff;
            padding: 25px;
            border-radius: 8px;
            border: 1px solid #e5e5e5;
        }
        h2 {
            text-align: center;
            margin-bottom: 10px;
            color: #333;
        }
        .customer-info {
            margin: 15px 0;
            font-size: 14px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 14px;
        }
        table th {
            background: #1976d2;
            color: #ffffff;
            text-align: left;
            padding: 10px;
        }
        table td {
            border: 1px solid #dcdcdc;
            padding: 10px;
            color: #555;
        }
        .total {
            font-weight: bold;
            font-size: 16px;
            text-align: right;
            padding-top: 10px;
        }
        .footer {
            text-align: center;
            font-size: 12px;
            color: #888;
            margin-top: 25px;
        }
    </style>
</head>

<body>
    <div class="container">

        <h2>Invoice: {{ $invoice->invoice_number }}</h2>

        <p class="customer-info">
            Hi <strong>{{ $invoice->customer->name }}</strong>,<br>
            Here is the summary of your recent payments.
        </p>

        <p>
            <strong>Invoice Date:</strong> {{ date('Y-m-d', strtotime($invoice->invoice_date)) }}<br>
            <strong>Total USD Amount:</strong> ${{ number_format($invoice->total_amount_usd, 2) }}
        </p>

        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Reference No</th>
                    <th>Original Amount</th>
                    <th>USD Amount</th>
                </tr>
            </thead>

            <tbody>
                @foreach($payments as $payment)
                <tr>
                    <td>{{ $payment->payment_date }}</td>
                    <td>{{ $payment->reference_no }}</td>
                    <td>{{ $payment->amount }} {{ $payment->currency }}</td>
                    <td>${{ number_format($payment->amount_usd, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <p class="total">Total: ${{ number_format($invoice->total_amount_usd, 2) }}</p>

        <div class="footer">
            Thank you for your business!<br>
            This is an automated email — please do not reply.
        </div>
    </div>
</body>
</html>
