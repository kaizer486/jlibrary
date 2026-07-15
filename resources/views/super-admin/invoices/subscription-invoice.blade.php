<!DOCTYPE html>
<html>
<head>
    <title>Subscription {{ $invoice_number }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: 'Helvetica Neue', Arial, sans-serif;
            background: #f5f5f5;
            padding: 40px 20px;
            color: #1a1a1a;
        }
        
        .invoice-wrapper {
            max-width: 800px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            padding: 50px;
        }
        
        .invoice-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 3px solid #e8e0d8;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        
        .brand h1 {
            font-size: 28px;
            font-weight: 700;
            color: #1a1a1a;
            letter-spacing: -0.5px;
        }
        
        .brand span {
            display: block;
            font-size: 13px;
            color: #888;
            font-weight: 400;
            margin-top: 2px;
        }
        
        .invoice-title {
            text-align: right;
        }
        
        .invoice-title h2 {
            font-size: 22px;
            font-weight: 700;
            color: #1a1a1a;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .invoice-title p {
            font-size: 13px;
            color: #888;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            background: #faf8f6;
            padding: 20px 24px;
            border-radius: 6px;
            margin-bottom: 30px;
        }
        
        .info-item label {
            display: block;
            font-size: 11px;
            text-transform: uppercase;
            color: #888;
            font-weight: 600;
            letter-spacing: 0.3px;
            margin-bottom: 3px;
        }
        
        .info-item p {
            font-size: 14px;
            font-weight: 500;
            color: #1a1a1a;
        }
        
        .status {
            display: inline-block;
            padding: 3px 14px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        .status-completed { background: #e6f7e6; color: #1a7a3a; }
        .status-pending { background: #fff5e6; color: #b86b1f; }
        .status-failed { background: #fde8e8; color: #b33a3a; }
        .status-cancelled { background: #f0f0f0; color: #666; }
        
        .period-box {
            background: #f8f5f2;
            padding: 14px 20px;
            border-radius: 6px;
            margin-bottom: 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
        }
        
        .period-box .label {
            font-size: 11px;
            text-transform: uppercase;
            color: #888;
            font-weight: 600;
            letter-spacing: 0.3px;
        }
        
        .period-box .dates {
            font-size: 14px;
            font-weight: 500;
            color: #1a1a1a;
        }
        
        .period-box .plan {
            font-size: 14px;
            font-weight: 600;
            color: #1a1a1a;
        }
        
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        
        .items-table th {
            background: #f5f2ef;
            padding: 12px 16px;
            text-align: left;
            font-size: 12px;
            text-transform: uppercase;
            color: #666;
            font-weight: 600;
            border-bottom: 2px solid #e8e0d8;
        }
        
        .items-table td {
            padding: 14px 16px;
            border-bottom: 1px solid #eee;
            font-size: 14px;
            color: #1a1a1a;
        }
        
        .text-right { text-align: right; }
        .amount { font-weight: 600; }
        
        .total-section {
            display: flex;
            justify-content: flex-end;
            border-top: 2px solid #e8e0d8;
            padding-top: 20px;
            margin-top: 10px;
        }
        
        .total-box {
            text-align: right;
            padding: 10px 30px;
            background: #1a1a1a;
            border-radius: 6px;
            min-width: 200px;
        }
        
        .total-box .label {
            display: block;
            font-size: 12px;
            color: rgba(255,255,255,0.6);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .total-box .amount {
            display: block;
            font-size: 26px;
            font-weight: 700;
            color: #ffffff;
            margin-top: 2px;
        }
        
        .invoice-footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            font-size: 12px;
            color: #999;
        }
        
        .invoice-footer strong { color: #1a1a1a; }
        
        @media (max-width: 600px) {
            .invoice-wrapper { padding: 24px; }
            .invoice-header { flex-direction: column; gap: 12px; align-items: flex-start; }
            .invoice-title { text-align: left; width: 100%; }
            .info-grid { grid-template-columns: 1fr; gap: 10px; }
            .period-box { flex-direction: column; text-align: center; }
            .total-section { justify-content: center; }
            .total-box { min-width: unset; width: 100%; text-align: center; }
            .invoice-footer { flex-direction: column; gap: 6px; text-align: center; }
        }
    </style>
</head>
<body>

<div class="invoice-wrapper">
    
    <div class="invoice-header">
        <div class="brand">
            <h1>📚 JLibrary</h1>
            <span>Digital Library Platform</span>
        </div>
        <div class="invoice-title">
            <h2>Subscription</h2>
            <p>#{{ $invoice_number }}</p>
        </div>
    </div>
    
    <div class="info-grid">
        <div class="info-item">
            <label>Date</label>
            <p>{{ $date->format('F d, Y H:i:s') }}</p>
        </div>
        <div class="info-item">
            <label>Status</label>
            <p><span class="status status-{{ $status }}">{{ ucfirst($status) }}</span></p>
        </div>
        <div class="info-item">
            <label>Subscriber</label>
            <p>{{ $user->full_name ?? $user->name ?? 'N/A' }}</p>
            <p style="font-weight:400; font-size:13px; color:#888;">{{ $user->email ?? 'N/A' }}</p>
        </div>
        <div class="info-item">
            <label>Method</label>
            <p>{{ ucfirst($method ?? 'N/A') }}</p>
        </div>
    </div>
    
    <div class="period-box">
        <div>
            <span class="label">Billing Period</span>
            <div class="dates">
                {{ isset($billing_period_start) ? $billing_period_start->format('M d, Y') : 'N/A' }}
                —
                {{ isset($billing_period_end) ? $billing_period_end->format('M d, Y') : 'N/A' }}
            </div>
        </div>
        <div>
            <span class="label">Plan</span>
            <div class="plan">{{ $plan_name ?? 'Subscription' }}</div>
        </div>
    </div>
    
    <table class="items-table">
        <thead>
            <tr>
                <th>Plan</th>
                <th>Currency</th>
                <th class="text-right">Amount</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ $plan_name ?? 'Subscription Plan' }}</td>
                <td>{{ $currency ?? 'TSh' }}</td>
                <td class="text-right amount">{{ $currency ?? 'TSh' }} {{ number_format($amount ?? 0, 2) }}</td>
            </tr>
            @if(isset($reference))
            <tr>
                <td colspan="2" style="font-size:13px; color:#888;">
                    <strong>Reference:</strong> {{ $reference }}
                </td>
                <td></td>
            </tr>
            @endif
        </tbody>
    </table>
    
    <div class="total-section">
        <div class="total-box">
            <span class="label">Total</span>
            <span class="amount">{{ $currency ?? 'TSh' }} {{ number_format($amount ?? 0, 2) }}</span>
        </div>
    </div>
    
    <div class="invoice-footer">
        <span><strong>JLibrary</strong> · Digital Library Platform</span>
        <span>Thank you for subscribing</span>
    </div>
    
</div>

</body>
</html>