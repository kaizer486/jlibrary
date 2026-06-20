<!DOCTYPE html>
<html>
<head>
    <title>Invoice {{ $invoice_number }}</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 20px; }
        .header { text-align: center; margin-bottom: 30px; }
        .company-name { font-size: 24px; font-weight: bold; color: #6366f1; }
        .invoice-title { font-size: 18px; color: #666; margin-top: 5px; }
        .invoice-info { margin-bottom: 30px; }
        .invoice-info table { width: 100%; }
        .invoice-info td { padding: 5px; }
        .items-table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        .items-table th, .items-table td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        .items-table th { background-color: #f3f4f6; }
        .total { text-align: right; font-size: 18px; font-weight: bold; }
        .footer { text-align: center; margin-top: 50px; color: #999; font-size: 12px; }
    </style>
</head>
<body>
    <div class="header">
        <div class="company-name">JLIBRARY</div>
        <div class="invoice-title">PAYMENT INVOICE</div>
    </div>
    
    <div class="invoice-info">
        <table>
            <tr>
                <td width="50%">
                    <strong>Invoice Number:</strong> {{ $invoice_number }}<br>
                    <strong>Date:</strong> {{ $date->format('F d, Y H:i:s') }}<br>
                    <strong>Status:</strong> <span style="color: green;">{{ ucfirst($status) }}</span>
                </td>
                <td width="50%">
                    <strong>Bill To:</strong><br>
                    {{ $user->full_name }}<br>
                    {{ $user->email }}
                </td>
            </tr>
        </table>
    </div>
    
    <table class="items-table">
        <thead>
            <tr>
                <th>Description</th>
                <th>Payment Method</th>
                <th class="text-right">Amount</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ $item_name }} - {{ $type }}</td>
                <td>{{ ucfirst($method) }}</td>
                <td class="text-right">TSh {{ number_format($amount, 2) }}</td>
            </tr>
        </tbody>
    </table>
    
    <div class="total">
        Total: TSh {{ number_format($amount, 2) }}
    </div>
    
    <div class="footer">
        Thank you for using JLIBRARY!<br>
        This is a system-generated invoice.
    </div>
</body>
</html>