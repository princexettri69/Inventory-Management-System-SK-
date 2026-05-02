<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #{{ $sale->invoice_number }}</title>
    <style>
        @media print {
            .no-print {
                display: none !important;
            }
            @page {
                size: A5 landscape;
                margin: 0;
            }
            body {
                margin: 5mm 10mm;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
        
        /* Action Buttons */
        .action-buttons {
            text-align: center;
            margin-bottom: 20px;
            padding: 10px;
            background: #f8f9fa;
            border-bottom: 1px solid #ddd;
        }
        .btn {
            padding: 8px 15px;
            margin: 0 5px;
            font-size: 12pt;
            cursor: pointer;
            border: none;
            border-radius: 4px;
            color: white;
            text-decoration: none;
            display: inline-block;
            font-family: Arial, sans-serif;
        }
        .btn-print { background-color: #007bff; }
        .btn-print:hover { background-color: #0056b3; }
        .btn-whatsapp { background-color: #25D366; }
        .btn-whatsapp:hover { background-color: #1ebe5d; }

        body {
            font-family: Arial, sans-serif;
            font-size: 10pt;
            line-height: normal;
            color: #000;
            max-width: 210mm;
            margin: 0 auto;
            background: #fff;
            padding: 10px;
        }

        .container {
            width: 100%;
            border: 0px solid #000;
        }

        /* HEADER GRID */
        .header {
            display: flex;
            width: 100%;
            margin-bottom: 2px;
            border-bottom: 2px solid #000;
            padding-bottom: 5px;
        }

        .header-left {
            width: 60%;
            display: flex;
            align-items: center;
        }

        .logo-box {
            border: 3px double #000;
            width: 60px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24pt;
            font-weight: bold;
            font-family: 'Times New Roman', serif;
            margin-right: 10px;
        }

        .company-info {
            text-align: left;
        }

        .company-name {
            font-family: 'Times New Roman', serif;
            font-size: 16pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 2px;
        }

        .company-desc {
            font-size: 8pt;
            margin-bottom: 2px;
        }

        .company-address {
            font-size: 8pt;
        }

        .header-right {
            width: 40%;
            text-align: right;
            padding-left: 20px;
            font-size: 9pt;
        }

        .header-row {
            display: flex;
            margin-bottom: 5px;
            align-items: flex-end;
        }

        .header-right .header-row {
            justify-content: flex-end !important;
        }

        .header-label {
            white-space: nowrap;
            margin-right: 5px;
        }

        .header-value {
            border-bottom: 1px dotted #000;
            flex-grow: 1;
            padding-left: 5px;
        }

        .header-right .header-value {
            flex-grow: 0;
            min-width: 150px;
        }
            min-width: 150px;
        }

        /* INVOICE NO ROW */
        .invoice-row {
            margin-top: 2px;
            margin-bottom: 5px;
            font-weight: bold;
            font-size: 9pt;
            display: flex;
            align-items: center;
        }

        .invoice-label {
            margin-right: 5px;
            font-style: italic;
        }

        .invoice-value {
             border-bottom: 1px dotted #000;
             min-width: 100px;
             display: inline-block;
        }

        /* TABLE */
        table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #000;
            margin-bottom: 5px;
        }

        th {
            border: 1px solid #000;
            padding: 5px;
            text-align: center;
            font-weight: bold;
            text-align: center;
            font-weight: bold;
            background: #f0f0f0;
            font-size: 8pt;
            white-space: nowrap;
        }

        td {
            border-left: 1px solid #000;
            border-right: 1px solid #000;
            border-bottom: 1px solid #000;
            padding: 4px 5px;
            font-size: 8pt;
            vertical-align: middle;
            height: 20px; /* Minimum height for lines */
        }

        .col-name { width: 43%; text-align: left; }
        .col-name { width: 43%; text-align: left; }
        .col-qty { width: 8%; text-align: center; }
        .col-price { width: 16%; text-align: right; }
        .col-disc { width: 15%; text-align: right; }
        .col-total { width: 18%; text-align: right; }

        /* FOOTER GRID */
        .footer {
            display: flex;
            margin-top: 5px;
            align-items: flex-start;
        }

        .footer-left {
            width: 25%;
            text-align: center;
            font-size: 9pt;
        }

        .footer-center {
            width: 45%;
            padding: 0 10px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .disclaimer-box {
            border: 1px solid #000;
            border-radius: 5px;
            padding: 8px;
            font-size: 8pt;
            text-align: center;
            background: #f5f5f5;
            width: 100%;
        }

        .footer-right {
            width: 30%;
        }

        .amount-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
            font-size: 10pt;
            font-weight: bold;
        }

        .amount-label {
            text-align: left;
        }

        .amount-value {
            text-align: right;
            border-bottom: 1px solid #ccc;
            min-width: 80px;
        }

        .signature-space {
            height: 40px;
            margin-top: 5px;
        }

    </style>
</head>
<body>
    @php
        $whatsappNumber = '';
        if($sale->customer && $sale->customer->phone) {
            $phone = preg_replace('/[^0-9]/', '', $sale->customer->phone);
            if(strlen($phone) == 10) {
                $whatsappNumber = '977' . $phone;
            } else {
                $whatsappNumber = $phone;
            }
        }
        
        $waMessage = "Hello " . ($sale->customer->name ?? 'Customer') . ",\n\n";
        $waMessage .= "Here is your Estimate Bill from S.K. Trade & Suppliers.\n";
        $waMessage .= "Invoice No: *" . $sale->invoice_number . "*\n";
        $waMessage .= "Net Total: *Rs. " . number_format($sale->total, 2) . "*\n";
        
        $oldBalance = 0;
        if($sale->customer_id) {
            $transactions = \App\Models\FinanceTransaction::with('category')
                ->where('customer_id', $sale->customer_id)
                ->where('created_at', '<', $sale->created_at)
                ->get();
            $debit = 0;
            $credit = 0;
            foreach ($transactions as $t) {
                if ($t->category && $t->category->type === \App\Enums\FinanceCategoryType::Income) {
                    $debit += $t->amount;
                } else {
                    $credit += $t->amount;
                }
            }
            $oldBalance = $debit - $credit;
            
            if($oldBalance > 0) {
                $waMessage .= "Previous Balance: *Rs. " . number_format($oldBalance, 2) . "*\n";
                $waMessage .= "Total Due: *Rs. " . number_format($oldBalance + max(0, $sale->total - $sale->cash_received), 2) . "*\n";
            }
        }
        
        $waMessage .= "\nThank you for doing business with us!\n_Itahari-6, Dharan Line | Ph: 9705451066_";
        $waUrl = "https://wa.me/" . $whatsappNumber . "?text=" . urlencode($waMessage);
    @endphp

    <div class="no-print action-buttons">
        <button onclick="window.print()" class="btn btn-print">🖨️ Print Receipt</button>
        @if($whatsappNumber)
            <a href="{{ $waUrl }}" target="_blank" class="btn btn-whatsapp">💬 Send via WhatsApp</a>
        @else
            <button class="btn btn-whatsapp" style="opacity: 0.5; cursor: not-allowed;" title="Customer phone number is missing or invalid">💬 Send via WhatsApp (No Phone)</button>
        @endif
    </div>

    <div class="container">
        <!-- Watermark/Title -->
        <div style="text-align: center; margin-bottom: 10px;">
            <h1 style="margin: 0; padding: 0; font-size: 14pt; text-decoration: underline;">ESTIMATE BILL</h1>
        </div>

        <!-- Header -->
        <div class="header">
            <div class="header-left">
                <img src="{{ asset('images/logo.png') }}" alt="Logo" class="logo-box" style="border:none; width: 100px; height: auto; object-fit: contain; margin-right: 15px;">
                <div class="company-info">
                    <div class="company-desc">Hardware, Construction Materials & Suppliers</div>
                    <div class="company-address">
                        Itahari-6, Dharan Line<br>
                        Ph. 9705451066
                    </div>
                </div>
            </div>
            <div class="header-right">
                <div class="header-row">
                    <span class="header-label">Invoice No:</span>
                    <span class="header-value" style="font-weight: bold;">{{ $sale->invoice_number }}</span>
                </div>
                <div class="header-row">
                    <span class="header-label">Date:</span>
                    <span class="header-value">{{ $sale->sale_date->format('Y-m-d') }}</span>
                </div>
                <div class="header-row">
                    <span class="header-label">Buyer:</span>
                    <span class="header-value">{{ $sale->customer->name ?? 'Guest' }}</span>
                </div>
                <div class="header-row">
                    <span class="header-label">Buyer's PAN:</span>
                    <span class="header-value">{{ $sale->customer->pan_number ?? '-' }}</span>
                </div>
            </div>
        </div>

        <!-- Table -->
        <table style="margin-top: 10px;">
            <thead>
                <tr>
                    <th style="width: 5%;">S.N</th>
                    <th class="col-name">Description of Goods</th>
                    <th class="col-qty">Qty</th>
                    <th class="col-qty">Unit</th>
                    <th class="col-price">Rate</th>
                    <th class="col-total">Total Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sale->items as $index => $item)
                @php
                    $finalPrice = $item->unit_price - $item->discount;
                @endphp
                <tr>
                    <td style="text-align: center;">{{ $index + 1 }}</td>
                    <td class="col-name">
                        {{ $item->product->name }}
                        @if($item->discount > 0)
                        <div style="font-size: 7pt; color: #666;">(Discount: @money($item->discount) / unit)</div>
                        @endif
                    </td>
                    <td class="col-qty">{{ $item->quantity }}</td>
                    <td class="col-qty">{{ $item->product->unit->symbol ?? '' }}</td>
                    <td class="col-price">@money($finalPrice)</td>
                    <td class="col-total">@money($item->subtotal)</td>
                </tr>
                @endforeach

                {{-- Fill empty rows to maintain size --}}
                @for($i = 0; $i < max(0, 8 - count($sale->items)); $i++)
                <tr>
                    <td style="text-align: center;">{{ count($sale->items) + $i + 1 }}</td>
                    <td>&nbsp;</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                @endfor
            </tbody>
        </table>

        <!-- Footer -->
        <div class="footer">
            <div class="footer-left" style="width: 65%;">
                <div style="margin-bottom: 10px;">
                    <strong>In Words:</strong> 
                    <span style="text-transform: capitalize; font-style: italic;">
                        {{ amount_to_words($sale->total) }} Only.
                    </span>
                </div>
                
                <div style="display: flex; justify-content: space-between; margin-top: 30px;">
                    <div style="text-align: center; width: 45%;">
                        <div style="border-top: 1px solid #000; margin-top: 40px; padding-top: 5px;">Receiver's Signature</div>
                    </div>
                    <div style="text-align: center; width: 45%;">
                        <div style="border-top: 1px solid #000; margin-top: 40px; padding-top: 5px;">Authorized Signature</div>
                    </div>
                </div>

                <div class="disclaimer-box" style="margin-top: 20px; text-align: left; font-size: 7pt;">
                    <strong>Terms & Conditions:</strong><br>
                    1. Goods once sold will not be taken back.<br>
                    2. Any discrepancies should be reported within 24 hours.
                </div>
            </div>

            <div class="footer-right" style="width: 35%; padding-left: 15px;">
                <div class="amount-row">
                    <span class="amount-label">Gross Amount:</span>
                    <span class="amount-value">@money($sale->total + $sale->global_discount)</span>
                </div>
                @if($sale->global_discount > 0)
                <div class="amount-row">
                    <span class="amount-label">Global Disc:</span>
                    <span class="amount-value">- @money($sale->global_discount)</span>
                </div>
                @endif
                
                <div class="amount-row" style="font-size: 11pt; border-top: 1px solid #000; margin-top: 5px; padding-top: 5px;">
                    <span class="amount-label">NET TOTAL:</span>
                    <span class="amount-value">@money($sale->total)</span>
                </div>

                @if($sale->customer_id && isset($oldBalance) && $oldBalance > 0)
                    <div class="amount-row" style="font-size: 9pt; border-top: 1px dotted #000; margin-top: 5px; padding-top: 5px;">
                        <span class="amount-label">Previous Balance:</span>
                        <span class="amount-value">@money($oldBalance)</span>
                    </div>
                @endif
            </div>
        </div>
    </div>

</body>
</html>
