<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $invoice->get('invoice_number') }}</title>
    <style>
        @page { margin: 0; }

        body {
            margin: 0;
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11px;
            color: #2b2420;
            background-color: #ece5dd;
        }

        table { border-collapse: collapse; }
        .full { width: 100%; }
        .text-right { text-align: right; }

        .canvas { padding: 28px 32px; }

        .card {
            background-color: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(43, 36, 32, 0.15);
        }

        .card-accent {
            height: 5px;
            background-color: #d97757;
        }

        .card-inner { padding: 42px 44px 46px; }

        .brand-mark {
            width: 42px;
            height: 42px;
            background-color: #d97757;
            border-radius: 12px;
            color: #ffffff;
            font-size: 15px;
            font-weight: bold;
            text-align: center;
        }

        .invoice-title {
            font-size: 30px;
            font-weight: bold;
            color: #2b2420;
            line-height: 1;
        }

        .invoice-number {
            font-size: 11px;
            color: #8a8177;
            margin-top: 5px;
        }

        .status-pill {
            display: inline-block;
            padding: 4px 12px;
            border: 1px solid {{ $statusColors['text'] }};
            border-radius: 999px;
            font-size: 9px;
            font-weight: bold;
            letter-spacing: 0.6px;
            text-transform: uppercase;
            color: {{ $statusColors['text'] }};
        }

        .header-rule {
            border-bottom: 1px solid #e5ddd3;
            margin-top: 22px;
            margin-bottom: 30px;
        }

        .section-label {
            font-size: 9px;
            font-weight: bold;
            letter-spacing: 0.7px;
            text-transform: uppercase;
            color: #8a8177;
            margin-bottom: 7px;
        }

        .party-name { font-size: 13.5px; font-weight: bold; color: #2b2420; }
        .party-line { font-size: 10.5px; color: #6b6259; margin-top: 3px; }

        .meta-table { margin-top: 30px; }
        .meta-pair {
            padding: 10px 0;
            border-bottom: 1px solid #e5ddd3;
            font-size: 11px;
        }
        .meta-pair .label { color: #8a8177; }
        .meta-pair .value { float: right; font-weight: bold; }

        .line-items {
            width: 100%;
            border-collapse: collapse;
            margin-top: 32px;
        }

        .line-items thead th {
            text-align: left;
            font-size: 9px;
            font-weight: bold;
            letter-spacing: 0.6px;
            text-transform: uppercase;
            color: #8a8177;
            border-bottom: 1px solid #e5ddd3;
            padding: 0 0 9px;
        }

        .line-items tbody td {
            padding: 11px 0;
            font-size: 11px;
            border-bottom: 1px solid #e5ddd3;
        }

        .totals-box {
            margin-top: 6px;
            width: 230px;
            float: right;
        }

        .totals-box table { width: 100%; }
        .totals-box td { padding: 6px 0; font-size: 11px; color: #6b6259; }

        .total-row td {
            border-top: 1px solid #e5ddd3;
            padding-top: 12px;
            font-size: 19px;
            font-weight: bold;
            color: #d97757;
        }

        .notes {
            clear: both;
            margin-top: 80px;
            padding-top: 16px;
            border-top: 1px solid #e5ddd3;
            font-size: 10.5px;
            color: #6b6259;
        }

        .footer-note {
            clear: both;
            margin-top: 80px;
            text-align: center;
            font-size: 10px;
            color: #a39a8f;
        }
    </style>
</head>
<body>
    <div class="canvas">
        <div class="card">
            <div class="card-accent"></div>
            <div class="card-inner">
                <table class="full">
                    <tr>
                        <td style="width: 65%;">
                            <table>
                                <tr>
                                    <td style="width: 42px;">
                                        <table style="height: 42px;"><tr><td class="brand-mark">{{ $businessInitials }}</td></tr></table>
                                    </td>
                                    <td style="padding-left: 14px; vertical-align: middle;">
                                        <div class="invoice-title">Invoice</div>
                                    </td>
                                </tr>
                            </table>
                            <div class="invoice-number">{{ $invoice->get('invoice_number') }}</div>
                        </td>
                        <td style="width: 35%; text-align: right; vertical-align: top;">
                            <span class="status-pill">{{ $invoice->get('status') }}</span>
                        </td>
                    </tr>
                </table>

                <div class="header-rule"></div>

                <table class="full">
                    <tr>
                        <td style="width: 48%; vertical-align: top;">
                            <div class="section-label">From</div>
                            <div class="party-name">{{ $business->get('displayName') }}</div>
                            <div class="party-line">{{ $business->get('email') }}</div>
                            @if ($business->get('phone'))
                                <div class="party-line">{{ $business->get('phone') }}</div>
                            @endif
                            @if ($business->get('address'))
                                <div class="party-line">{{ $business->get('address') }}</div>
                            @endif
                            @if ($business->get('taxId'))
                                <div class="party-line">Tax ID: {{ $business->get('taxId') }}</div>
                            @endif
                        </td>
                        <td style="width: 4%;"></td>
                        <td style="width: 48%; vertical-align: top;">
                            <div class="section-label">Bill to</div>
                            <div class="party-name">{{ $client->get('name') ?? '—' }}</div>
                            @if ($client->get('email'))
                                <div class="party-line">{{ $client->get('email') }}</div>
                            @endif
                            @if ($client->get('phone'))
                                <div class="party-line">{{ $client->get('phone') }}</div>
                            @endif
                            @if ($client->get('address'))
                                <div class="party-line">{{ $client->get('address') }}</div>
                            @endif
                        </td>
                    </tr>
                </table>

                <table class="meta-table full">
                    <tr>
                        <td style="width: 44%;">
                            <div class="meta-pair">
                                <span class="value">{{ \Carbon\Carbon::parse($invoice->get('issue_date'))->format('d M, Y') }}</span>
                                <span class="label">Issue date</span>
                            </div>
                        </td>
                        <td style="width: 12%;"></td>
                        <td style="width: 44%;">
                            <div class="meta-pair">
                                <span class="value">{{ $invoice->get('due_date') ? \Carbon\Carbon::parse($invoice->get('due_date'))->format('d M, Y') : '—' }}</span>
                                <span class="label">Due date</span>
                            </div>
                        </td>
                    </tr>
                </table>

                <table class="line-items">
                    <thead>
                        <tr>
                            <th>Description</th>
                            <th class="text-right" style="width: 60px;">Qty</th>
                            <th class="text-right" style="width: 90px;">Unit price</th>
                            <th class="text-right" style="width: 90px;">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($lineItems as $lineItem)
                            <tr>
                                <td>{{ $lineItem->get('description') }}</td>
                                <td class="text-right">{{ rtrim(rtrim(number_format($lineItem->get('quantity'), 2), '0'), '.') }}</td>
                                <td class="text-right">&#8377;{{ number_format($lineItem->get('unit_price'), 2) }}</td>
                                <td class="text-right">&#8377;{{ number_format($lineItem->get('line_total'), 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" style="text-align: center; color: #a39a8f; padding: 20px 0;">No line items.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="totals-box">
                    <table>
                        <tr>
                            <td>Subtotal</td>
                            <td class="text-right">&#8377;{{ number_format($invoice->get('subtotal'), 2) }}</td>
                        </tr>
                        <tr>
                            <td>Discount</td>
                            <td class="text-right">&minus;&#8377;{{ number_format($invoice->get('discount_amount'), 2) }}</td>
                        </tr>
                        <tr>
                            <td>Tax ({{ rtrim(rtrim(number_format($invoice->get('tax_rate'), 2), '0'), '.') }}%)</td>
                            <td class="text-right">&#8377;{{ number_format($invoice->get('tax_amount'), 2) }}</td>
                        </tr>
                        <tr class="total-row">
                            <td>Total due</td>
                            <td class="text-right">&#8377;{{ number_format($invoice->get('total'), 2) }}</td>
                        </tr>
                    </table>
                </div>

                @if ($invoice->get('notes'))
                    <div class="notes">
                        <div class="section-label">Notes</div>
                        {{ $invoice->get('notes') }}
                    </div>
                @else
                    <div class="footer-note">Thank you for your business.</div>
                @endif
            </div>
        </div>
    </div>
</body>
</html>
