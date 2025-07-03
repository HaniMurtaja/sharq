{{-- resources/views/admin/pages/accounting/invoice-pdf.blade.php --}}
<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice {{ $invoice->invoice_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            direction: rtl;
        }

        .invoice-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background: white;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #f46624;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }

        .company-info {
            text-align: right;
        }

        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: #f46624;
            margin-bottom: 10px;
        }

        .company-details {
            font-size: 11px;
            color: #666;
            line-height: 1.6;
        }

        .invoice-title {
            text-align: center;
            background: #f46624;
            color: white;
            padding: 15px;
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 30px;
        }

        .invoice-details {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }

        .bill-to, .invoice-info {
            width: 48%;
        }

        .bill-to h3, .invoice-info h3 {
            background: #f8f9fa;
            padding: 10px;
            margin-bottom: 15px;
            border-right: 4px solid #f46624;
            font-size: 14px;
        }

        .details-content {
            padding: 0 10px;
        }

        .details-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            padding: 5px 0;
            border-bottom: 1px dotted #ddd;
        }

        .details-row:last-child {
            border-bottom: none;
        }

        .label {
            font-weight: bold;
            color: #555;
        }

        .value {
            color: #333;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .items-table th {
            background: #f46624;
            color: white;
            padding: 12px 8px;
            text-align: center;
            font-weight: bold;
            border: 1px solid #ddd;
        }

        .items-table td {
            padding: 10px 8px;
            text-align: center;
            border: 1px solid #ddd;
            background: #fafafa;
        }

        .items-table tr:nth-child(even) td {
            background: #f5f5f5;
        }

        .totals-section {
            float: left;
            width: 300px;
            margin-bottom: 30px;
        }

        .totals-table {
            width: 100%;
            border-collapse: collapse;
        }

        .totals-table td {
            padding: 10px;
            border: 1px solid #ddd;
        }

        .totals-table .label-cell {
            background: #f8f9fa;
            font-weight: bold;
            text-align: right;
            width: 60%;
        }

        .totals-table .amount-cell {
            background: white;
            text-align: left;
            direction: ltr;
        }

        .total-row {
            background: #f46624 !important;
            color: white !important;
            font-weight: bold;
            font-size: 14px;
        }

        .total-row td {
            background: #f46624 !important;
            color: white !important;
        }

        .payment-info {
            clear: both;
            background: #f8f9fa;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 30px;
        }

        .payment-info h3 {
            color: #f46624;
            margin-bottom: 15px;
            font-size: 16px;
        }

        .payment-details {
            display: flex;
            justify-content: space-between;
        }

        .payment-column {
            width: 48%;
        }

        .footer {
            text-align: center;
            margin-top: 40px;
            padding-top: 20px;
            border-top: 2px solid #f46624;
            color: #666;
            font-size: 11px;
        }

        .qr-section {
            text-align: center;
            margin: 30px 0;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 5px;
        }

        .qr-code {
            display: inline-block;
            margin: 10px 0;
        }

        .qr-code img {
            width: 150px;
            height: 150px;
        }

        .status-badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            color: white;
            font-weight: bold;
            font-size: 11px;
            text-transform: uppercase;
        }

        .status-paid {
            background: #28a745;
        }

        .status-unpaid {
            background: #dc3545;
        }

        .status-pending {
            background: #ffc107;
            color: #333;
        }

        @media print {
            .invoice-container {
                max-width: none;
                margin: 0;
                padding: 15px;
            }
        }

        .arabic {
            font-family: 'Arial Unicode MS', Arial, sans-serif;
        }

        .english {
            direction: ltr;
            text-align: left;
        }
    </style>
</head>
<body>
    <div class="invoice-container">
        <!-- Header -->
        <div class="header">
            <div class="company-info">
                <div class="company-name arabic">{{ $settings->company_name }}</div>
                <div class="company-details arabic">
                    @if($settings->commercial_registration)
                        <div>رقم السجل التجاري: {{ $settings->commercial_registration }}</div>
                    @endif
                    @if($settings->tax_id)
                        <div>الرقم الضريبي: {{ $settings->tax_id }}</div>
                    @endif
                    @if($settings->address)
                        <div>العنوان: {{ $settings->address }}</div>
                    @endif
                    @if($settings->phone)
                        <div>الهاتف: {{ $settings->phone }}</div>
                    @endif
                    @if($settings->email)
                        <div>البريد الإلكتروني: {{ $settings->email }}</div>
                    @endif
                </div>
            </div>
            
            @if($invoice->status == 'paid')
                <span class="status-badge status-paid">مدفوعة</span>
            @elseif($invoice->status == 'confirmed_sent_unpaid')
                <span class="status-badge status-unpaid">غير مدفوعة</span>
            @else
                <span class="status-badge status-pending">قيد المراجعة</span>
            @endif
        </div>

        <!-- Invoice Title -->
        <div class="invoice-title arabic">
            فاتورة ضريبية / Tax Invoice
        </div>

        <!-- Invoice Details -->
        <div class="invoice-details">
            <div class="bill-to">
                <h3 class="arabic">فاتورة إلى / Bill To:</h3>
                <div class="details-content">
                    <div class="details-row">
                        <span class="label arabic">اسم العميل:</span>
                        <span class="value">{{ $invoice->client->full_name }}</span>
                    </div>
                    @if($invoice->client->email)
                    <div class="details-row">
                        <span class="label arabic">البريد الإلكتروني:</span>
                        <span class="value english">{{ $invoice->client->email }}</span>
                    </div>
                    @endif
                    @if($invoice->client->phone)
                    <div class="details-row">
                        <span class="label arabic">الهاتف:</span>
                        <span class="value">{{ $invoice->client->phone }}</span>
                    </div>
                    @endif
                    @if($invoice->client->client?->account_number)
                    <div class="details-row">
                        <span class="label arabic">رقم الحساب:</span>
                        <span class="value">{{ $invoice->client->client->account_number }}</span>
                    </div>
                    @endif
                </div>
            </div>

            <div class="invoice-info">
                <h3 class="arabic">تفاصيل الفاتورة / Invoice Details:</h3>
                <div class="details-content">
                    <div class="details-row">
                        <span class="label arabic">رقم الفاتورة:</span>
                        <span class="value english">{{ $invoice->invoice_number }}</span>
                    </div>
                    <div class="details-row">
                        <span class="label arabic">تاريخ الفاتورة:</span>
                        <span class="value">{{ $invoice->invoice_date->format('Y-m-d') }}</span>
                    </div>
                    <div class="details-row">
                        <span class="label arabic">تاريخ الاستحقاق:</span>
                        <span class="value">{{ $invoice->due_date->format('Y-m-d') }}</span>
                    </div>
                    <div class="details-row">
                        <span class="label arabic">العملة:</span>
                        <span class="value">{{ $invoice->currency }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Items Table -->
        <table class="items-table">
            <thead>
                <tr>
                    <th class="arabic">الوصف / Description</th>
                    <th class="arabic">الكمية / Qty</th>
                    <th class="arabic">السعر الوحدة / Unit Price</th>
                    <th class="arabic">المجموع / Total</th>
                    <th class="arabic">شهر الخدمة / Service Month</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->items as $item)
                <tr>
                    <td class="arabic">{{ $item->description }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td class="english">{{ number_format($item->unit_price, 2) }} {{ $invoice->currency }}</td>
                    <td class="english">{{ number_format($item->total_price, 2) }} {{ $invoice->currency }}</td>
                    <td>{{ \Carbon\Carbon::parse($item->service_month)->format('F Y') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Totals -->
        <div class="totals-section">
            <table class="totals-table">
                <tr>
                    <td class="label-cell arabic">المجموع الفرعي / Subtotal:</td>
                    <td class="amount-cell english">{{ number_format($invoice->subtotal, 2) }} {{ $invoice->currency }}</td>
                </tr>
                <tr>
                    <td class="label-cell arabic">ضريبة القيمة المضافة (15%) / VAT (15%):</td>
                    <td class="amount-cell english">{{ number_format($invoice->tax_amount, 2) }} {{ $invoice->currency }}</td>
                </tr>
                <tr class="total-row">
                    <td class="label-cell arabic">المجموع الكلي / Total Amount:</td>
                    <td class="amount-cell english">{{ number_format($invoice->total_amount, 2) }} {{ $invoice->currency }}</td>
                </tr>
            </table>
        </div>

        <!-- Payment Information -->
        <div class="payment-info">
            <h3 class="arabic">معلومات الدفع / Payment Information</h3>
            <div class="payment-details">
                <div class="payment-column">
                    @if($settings->bank_account)
                    <div class="details-row">
                        <span class="label arabic">رقم الحساب البنكي:</span>
                        <span class="value english">{{ $settings->bank_account }}</span>
                    </div>
                    @endif
                    @if($settings->iban)
                    <div class="details-row">
                        <span class="label arabic">رقم الآيبان:</span>
                        <span class="value english">{{ $settings->iban }}</span>
                    </div>
                    @endif
                </div>
                <div class="payment-column">
                    <div class="details-row">
                        <span class="label arabic">الحالة:</span>
                        <span class="value arabic">
                            @if($invoice->status == 'paid')
                                مدفوعة
                            @elseif($invoice->status == 'confirmed_sent_unpaid')
                                غير مدفوعة
                            @else
                                قيد المراجعة
                            @endif
                        </span>
                    </div>
                    @if($invoice->isOverdue())
                    <div class="details-row">
                        <span class="label arabic" style="color: red;">متأخرة بـ:</span>
                        <span class="value" style="color: red;">{{ $invoice->getDaysOverdue() }} يوم</span>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- QR Code Section -->
        @if($qrCodeUrl)
        <div class="qr-section">
            <h4 class="arabic">رمز الاستجابة السريعة للفاتورة الإلكترونية</h4>
            <div class="qr-code">
                <img src="{{ $qrCodeUrl }}" alt="QR Code" />
            </div>
            <div class="arabic" style="font-size: 10px; color: #666;">
                امسح الرمز للتحقق من صحة الفاتورة وفقاً لمتطلبات هيئة الزكاة والضريبة والجمارك
            </div>
        </div>
        @endif

        <!-- Notes -->
        @if($invoice->notes)
        <div style="margin: 20px 0; padding: 15px; background: #f8f9fa; border-radius: 5px;">
            <h4 class="arabic">ملاحظات / Notes:</h4>
            <p class="arabic">{{ $invoice->notes }}</p>
        </div>
        @endif

        <!-- Footer -->
        <div class="footer">
            <div class="arabic">
                شكراً لثقتكم بخدماتنا / Thank you for your business
            </div>
            <div style="margin-top: 10px;">
                تم إنشاء هذه الفاتورة تلقائياً في {{ now()->format('Y-m-d H:i') }}
            </div>
        </div>
    </div>
</body>
</html>