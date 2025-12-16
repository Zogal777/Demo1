<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice {{ $invoice->invoice_number }}</title>

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 13px;
            padding: 20px 35px;
            color: #000;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        .header-title {
            text-align: center;
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 15px;
        }

        .top-info {
            width: 100%;
            margin-bottom: 20px;
        }

        .top-info td {
            padding: 4px 0;
        }

        .box-table td, .box-table th {
            border: 1px solid #bbb;
            padding: 6px;
            font-size: 12px;
        }

        .title-row {
            background: #f2f2f2;
            font-weight: bold;
        }

        .items-table th, .items-table td {
            border: 1px solid #bbb;
            padding: 6px;
            font-size: 12px;
        }

        .items-table th {
            background: #f7f7f7;
        }

        .summary-table td {
            padding: 6px;
            font-size: 13px;
        }

        .right {
            text-align: right;
        }

        .bold {
            font-weight: bold;
        }

        .border-top {
            border-top: 1px solid #000;
        }

        .border-bottom {
            border-bottom: 1px solid #000;
        }

    </style>
</head>
<body>

<!-- HEADER -->
<div class="header-title">
    Invoice Nr. {{ $invoice->invoice_number }}
</div>

<table class="top-info">
    <tr>
        <td><strong>Invoice Date:</strong> {{ $invoice->invoice_date }}</td>
        <td class="right"><strong>Payment Due:</strong> {{ $invoice->payment_date }}</td>
    </tr>
</table>

<!-- COMPANY / CLIENT TABLE -->
<table class="box-table">
    <tr class="title-row">
        <th>Seller</th>
        <th>Buyer</th>
    </tr>

    <tr>
        <td><strong>{{ $invoice->company_name }}</strong></td>
        <td><strong>{{ $invoice->client_name }}</strong></td>
    </tr>

    <tr>
        <td>{{ $invoice->company_address }}</td>
        <td>{{ $invoice->client_address }}</td>
    </tr>

    <tr>
        <td><strong>Bank:</strong> {{ $invoice->company_bank }}</td>
        <td><strong>Bank:</strong> {{ $invoice->client_bank }}</td>
    </tr>
</table>

<!-- ITEMS -->
<table class="items-table" style="margin-top: 20px;">
    <thead>
    <tr>
        <th style="width: 5%">#</th>
        <th style="width: 20%">Item</th>
        <th style="width: 25%">Description</th>
        <th style="width: 12.5%">Qty</th>
        <th style="width: 12.5%">Price</th>
        <th style="width: 12.5%">VAT %</th>
        <th style="width: 12.5%">Total</th>
    </tr>
    </thead>

    <tbody>
    @foreach ($invoice->items as $index => $item)
        <tr>
            <td class="right">{{ $index + 1 }}</td>
            <td>{{ $item->item }}</td>
            <td>{{ $item->description }}</td>
            <td class="right">{{ $item->quantity }}</td>
            <td class="right">{{ number_format($item->price, 2) }} €</td>
            <td class="right">{{ $item->pvn }}%</td>
            <td class="right">
                {{ number_format($item->item_sum + ($item->item_sum * $item->pvn / 100), 2) }} €
            </td>
        </tr>
    @endforeach
    </tbody>
</table>


<!-- SUMMARY -->
<table class="summary-table" style="margin-top: 20px;">
    <tr>
        <td class="right bold">Total without VAT (€):</td>
        <td class="right">{{ number_format($invoice->total_without_pvn, 2) }} €</td>
    </tr>

    <tr>
        <td class="right bold">VAT {{ number_format($invoice->items->first()->pvn ?? 21, 0) }}% (€):</td>
        <td class="right">{{ number_format($invoice->total_pvn, 2) }} €</td>
    </tr>

    <tr class="border-top bold" style="font-size: 15px;">
        <td class="right">Total (€):</td>
        <td class="right bold">{{ number_format($invoice->total_sum, 2) }} €</td>
    </tr>
</table>

</body>
</html>
