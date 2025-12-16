<?php

namespace App\Http\Controllers\Service;

use App\Http\Controllers\Controller;
use App\Http\Requests\ServiceRequests\StoreServiceRequest;
use App\Http\Requests\ServiceRequests\UpdateServiceRequest;
use App\Models\Service;

class ServiceController extends Controller
{
    public function index()
    {
        return inertia('Services', [
            'services' => Service::latest()->get(),
        ]);
    }

    public function create()
    {
        return inertia('Services/CreateService');
    }

    public function store(StoreServiceRequest $request)
    {
        Service::create($request->validated());
        return redirect()->route('services.index');
    }

    public function edit(Service $service)
    {
        return inertia('Services/EditService', [
            'service' => $service,
        ]);
    }

    public function update(UpdateServiceRequest $request, Service $service)
    {
        $service->update($request->validated());
        return redirect()->route('services.index');
    }

    public function destroy(Service $service)
    {
        $service->delete();
        return redirect()->route('services.index');
    }
}
