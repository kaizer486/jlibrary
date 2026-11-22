<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice</title>
    <style>
        @page { margin: 40px; }
        body { 
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; 
            font-size: 13px; 
            color: #334155; 
            line-height: 1.6; 
            margin: 0; 
            padding: 0;
        }
        
        /* Header */
        .header-table { width: 100%; border-bottom: 2px solid #f1f5f9; padding-bottom: 20px; margin-bottom: 30px; }
        .header-table td { vertical-align: middle; }
        .logo { max-height: 80px; width: auto; margin-bottom: 5px; }
        .invoice-title { font-size: 32px; font-weight: 900; color: #7c3aed; letter-spacing: -0.5px; margin: 0; line-height: 1.1; }
        
        /* System Info */
        .system-info { text-align: right; font-size: 12px; color: #475569; line-height: 1.8; }
        .system-name { font-size: 16px; font-weight: 900; color: #1e293b; letter-spacing: 0.5px; }
        .system-info span { display: block; }

        /* Cards (Buyer & Seller) */
        .details-table { width: 100%; margin-bottom: 30px; }
        .details-table td { width: 50%; vertical-align: top; padding: 0 10px; }
        .details-table td:first-child { padding-left: 0; }
        .details-table td:last-child { padding-right: 0; }
        .info-card { background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 15px 20px; min-height: 120px; }
        .info-card h4 { margin: 0 0 10px 0; color: #7c3aed; font-size: 12px; text-transform: uppercase; letter-spacing: 1px; font-weight: bold; }
        .info-card p { margin: 0 0 6px 0; color: #334155; }
        .info-card strong { color: #0f172a; font-size: 14px; }
        
        /* Table */
        .items-table { width: 100%; border-collapse: separate; border-spacing: 0; margin-bottom: 30px; border: 1px solid #e2e8f0; border-radius: 8px; overflow: hidden; }
        .items-table th { background-color: #f8fafc; color: #475569; font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; padding: 12px 15px; text-align: left; border-bottom: 1px solid #e2e8f0; }
        .items-table td { padding: 15px; border-bottom: 1px solid #e2e8f0; vertical-align: middle; }
        .items-table tr:last-child td { border-bottom: none; }
        
        /* Totals */
        .totals-table { width: 100%; }
        .totals-table td { padding: 10px 15px; }
        .totals-table .label { text-align: right; color: #64748b; font-weight: 500; width: 80%; }
        .totals-table .value { text-align: right; font-weight: bold; color: #0f172a; width: 20%; }
        .grand-total .label { font-size: 16px; font-weight: 900; color: #0f172a; border-top: 2px solid #e2e8f0; padding-top: 15px; }
        .grand-total .value { font-size: 18px; font-weight: 900; color: #7c3aed; border-top: 2px solid #e2e8f0; padding-top: 15px; }

        /* Badges */
        .status-text { font-weight: bold; color: #334155; font-size: 14px; }
        .badge { display: inline-block; padding: 3px 8px; border-radius: 4px; font-size: 10px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.5px; }
        .badge-success { background-color: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; }
        .badge-warning { background-color: #fef3c7; color: #92400e; border: 1px solid #fde68a; }
        
        /* Footer */
        .footer { text-align: center; color: #94a3b8; font-size: 11px; margin-top: 50px; padding-top: 20px; border-top: 1px solid #f1f5f9; }
        .footer strong { color: #475569; }
    </style>
</head>
<body>

    <!-- HEADER SECTION -->
    <table class="header-table">
        <tr>
            <!-- Left: Logo & Invoice Title -->
            <td style="width: 50%;">
                <img src="{{ public_path('images/logo.jpeg') }}" alt="JLibrary Logo" class="logo">
                <h1 class="invoice-title">INVOICE</h1>
                <div style="color: #64748b; font-size: 12px; margin-top: 10px;">
                    Date: {{ $order->created_at->format('F d, Y') }}
                </div>
            </td>
            
            <!-- Right: System Contact Info -->
            <td style="width: 50%; text-align: right;">
                <div class="system-info">
                    <div class="system-name">JLibrary System</div>
                    <span><strong>Phone:</strong> +255 766408259</span>
                    <span><strong>Email:</strong> info@jlibrary.co.tz</span>
                    <span><strong>Support:</strong> contact@jlibrary.co.tz</span>
                    <span><strong>Web:</strong> www.jlibrary.co.tz</span>
                </div>
            </td>
        </tr>
    </table>

    <!-- BUYER & SELLER CARDS -->
    <table class="details-table">
        <tr>
            <!-- Buyer Info -->
            <td>
                <div class="info-card">
                    <h4>Billed To (Buyer)</h4>
                    <p><strong>{{ $order->user->full_name ?? 'Guest' }}</strong></p>
                    <p><strong>Phone:</strong> {{ $order->customer_phone }}</p>
                    <p><strong>Address:</strong> {{ $order->shipping_address }}</p>
                </div>
            </td>
            
            <!-- Seller Info -->
            <td>
                <div class="info-card">
                    <h4>Sold By (Seller)</h4>
                    <p><strong>{{ $order->book->uploader->full_name ?? 'JLibrary Seller' }}</strong></p>
                    
                    <!-- SHOW ACTUAL SELLER PHONE OR EMAIL -->
                    @php
                        $sellerPhone = $order->book->uploader->mpesa_phone ?? $order->book->uploader->phone ?? null;
                        $sellerEmail = $order->book->uploader->email ?? null;
                    @endphp
                    
                    @if($sellerPhone)
                        <p><strong>Phone:</strong> {{ $sellerPhone }}</p>
                    @elseif($sellerEmail)
                        <p><strong>Email:</strong> {{ $sellerEmail }}</p>
                    @else
                        <p><strong>Contact:</strong> Via JLibrary Support</p>
                    @endif
                    
                    <!-- FIXED ALIGNMENT: Using an invisible table forces perfect vertical alignment -->
                    <table style="margin-top: 12px; border: none; padding: 0; width: 100%;">
                        <tr>
                            <td style="padding: 0; vertical-align: middle; width: 55px;">
                                <span class="status-text">Status:</span>
                            </td>
                            <td style="padding: 0; vertical-align: middle;">
                                @if($order->isBuyerConfirmed() || $order->isSellerMarkedPaid())
                                    <span class="badge badge-success">PAID</span>
                                @else
                                    <span class="badge badge-warning">UNPAID</span>
                                @endif
                            </td>
                        </tr>
                    </table>

                </div>
            </td>
        </tr>
    </table>

    <!-- ORDER ITEMS -->
    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 50%;">Item Description</th>
                <th style="width: 15%; text-align: center;">Qty</th>
                <th style="width: 15%; text-align: right;">Price</th>
                <th style="width: 20%; text-align: right;">Total</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <div style="font-size: 16px; font-weight: bold; color: #0f172a; margin-bottom: 4px;">
                        {{ $order->book->title ?? 'Book Title Unavailable' }}
                    </div>
                    
                    {{-- HIDE CODE IF IT DOESN'T EXIST --}}
                    @if($order->book && $order->book->book_code)
                        <div style="font-size: 12px; color: #64748b;">
                            Code: {{ $order->book->book_code }}
                        </div>
                    @endif
                    
                    <div style="font-size: 12px; color: #64748b;">
                        Author: {{ $order->book->author ?? '—' }}
                    </div>
                </td>
                <td style="text-align: center; font-weight: bold;">{{ $order->quantity }}</td>
                <td style="text-align: right;">TSh {{ number_format($order->price_per_unit, 2) }}</td>
                <td style="text-align: right; font-weight: bold;">TSh {{ number_format($order->total, 2) }}</td>
            </tr>
        </tbody>
    </table>

    <!-- TOTALS -->
    <table class="totals-table">
        <tr class="grand-total">
            <td class="label">Grand Total:</td>
            <td class="value">TSh {{ number_format($order->total, 2) }}</td>
        </tr>
    </table>

    <!-- FOOTER -->
    <div class="footer">
        <p style="margin-bottom: 5px;"><strong>Thank you for choosing JLibrary!</strong></p>
        <p>If you have any questions about this invoice, please contact us at info@jlibrary.co.tz or call +255 766408259.</p>
    </div>

</body>
</html>