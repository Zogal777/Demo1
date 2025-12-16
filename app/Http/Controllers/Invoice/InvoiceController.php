<?php

namespace App\Http\Controllers\Invoice;

use App\Http\Controllers\Controller;
use App\Http\Requests\InvoiceRequests\StoreInvoiceRequest;
use App\Http\Requests\InvoiceRequests\UpdateInvoiceRequest;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Client;
use App\Models\Service;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;


class InvoiceController extends Controller
{
    public function index(\Illuminate\Http\Request $request)
    {
        $query = Invoice::with('items')->latest();

        if ($request->filled('company')) {
            $query->where('company_name', 'like', "%{$request->company}%");
        }

        if ($request->filled('client')) {
            $query->where('client_name', 'like', "%{$request->client}%");
        }

        if ($request->filled('invoice_date')) {
            $query->whereDate('invoice_date', $request->invoice_date);
        }

        return inertia('Invoices', [
            'invoices' => $query->get(),
            'filters' => $request->only(['company', 'client', 'invoice_date']),
        ]);
    }

    public function create()
    {
        return inertia('Invoices/CreateInvoice');
    }

    public function store(StoreInvoiceRequest $request)
    {
        DB::transaction(function () use ($request) {

            $year = Carbon::now()->year;

            $lastInvoice = Invoice::whereYear('created_at', $year)
                ->orderBy('id', 'desc')
                ->first();

            $nextNumber = $lastInvoice
                ? intval(substr($lastInvoice->invoice_number, -4)) + 1
                : 1;

            $invoiceNumber = sprintf('%d-%04d', $year, $nextNumber);

            $invoice = Invoice::create([
                ...$request->safe()->except('items'),
                'invoice_number' => $invoiceNumber,
            ]);

            $totalWithout = 0;
            $totalPvn = 0;

            foreach ($request->items as $item) {
                $sum = $item['price'] * $item['quantity'];
                $pvn = $sum * ($item['pvn'] / 100);

                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'item' => $item['name'],
                    'description' => $item['description'] ?? '',
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'pvn' => $item['pvn'],
                    'item_sum' => $sum,
                ]);

                $totalWithout += $sum;
                $totalPvn += $pvn;
            }

            $invoice->update([
                'total_without_pvn' => $totalWithout,
                'total_pvn' => $totalPvn,
                'total_sum' => $totalWithout + $totalPvn,
            ]);
        });

        return redirect()->route('invoices.index');
    }


    public function edit(Invoice $invoice)
    {
        return inertia('Invoices/EditInvoice', [
            'invoice' => $invoice->load('items'),
        ]);
    }

    public function update(UpdateInvoiceRequest $request, Invoice $invoice)
    {
        DB::transaction(function () use ($request, $invoice) {

            $invoice->update($request->safe()->except('items'));

            $invoice->items()->delete();

            $totalWithout = 0;
            $totalPvn = 0;

            foreach ($request->items as $item) {
                $sum = $item['price'] * $item['quantity'];
                $pvn = $sum * ($item['pvn'] / 100);

                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'item' => $item['name'],
                    'description' => $item['description'] ?? '',
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'pvn' => $item['pvn'],
                    'item_sum' => $sum,
                ]);

                $totalWithout += $sum;
                $totalPvn += $pvn;
            }

            $invoice->update([
                'total_without_pvn' => $totalWithout,
                'total_pvn' => $totalPvn,
                'total_sum' => $totalWithout + $totalPvn,
            ]);
        });

        return redirect()->route('invoices.index');
    }

    public function destroy(Invoice $invoice)
    {
        $invoice->items()->delete();
        $invoice->delete();

        return redirect()->route('invoices.index');
    }

    public function searchClients(\Illuminate\Http\Request $request)
    {
        return Client::where('business_name', 'like', "%{$request->q}%")
            ->limit(20)
            ->get();
    }

    public function searchServices(\Illuminate\Http\Request $request)
    {
        return Service::where('item_name', 'like', "%{$request->q}%")
            ->limit(20)
            ->get();
    }

    public function pdf(Invoice $invoice)
    {
        $invoice->load('items');

        return Pdf::loadView('pdf.invoice', [
            'invoice' => $invoice,
        ])->stream("invoice_{$invoice->invoice_number}.pdf");
    }

}
