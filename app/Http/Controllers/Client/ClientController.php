<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\ClientRequests\StoreClientRequest;
use App\Http\Requests\ClientRequests\UpdateClientRequest;
use App\Models\Client;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ClientController extends Controller
{
    public function index()
    {
        return Inertia::render('Clients', [
            'clients' => Client::with('contacts')
                ->orderByDesc('created_at') // новые записи сначала
                ->get(),
        ]);
    }

    public function create()
    {
        return Inertia::render('Clients/Create');
    }

    public function store(StoreClientRequest $request)
    {
        $client = Client::create($request->validated());

        foreach ($request->contacts ?? [] as $contact) {
            $client->contacts()->create($contact);
        }

        return redirect()
            ->route('clients.index')
            ->with('success', 'Client created successfully');
    }

    public function edit(Client $client)
    {
        return Inertia::render('Clients/Edit', [
            'client' => $client->load('contacts'),
        ]);
    }

    public function update(UpdateClientRequest $request, Client $client)
    {
        $client->update($request->validated());

        // replace contacts
        $client->contacts()->delete();

        foreach ($request->contacts ?? [] as $contact) {
            $client->contacts()->create($contact);
        }

        return redirect()
            ->route('clients.index')
            ->with('success', 'Client updated successfully');
    }

    public function destroy(Client $client)
    {
        $client->contacts()->delete();
        $client->delete();

        return redirect()
            ->route('clients.index')
            ->with('success', 'Client deleted successfully');
    }

    public function search(Request $request)
    {
        return Client::where('business_name', 'like', "%{$request->q}%")
            ->select('id', 'business_name', 'address', 'bank_account')
            ->orderByDesc('created_at') // тоже сортируем
            ->get();
    }
}
