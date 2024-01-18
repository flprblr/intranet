<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Service;
use App\Models\Store;
use Inertia\Inertia;
use Illuminate\Support\Facades\Request as Peticion;

class ServiceController extends Controller
{
    public function __construct() {
        $this->middleware('can:service.index')->only('index');
        $this->middleware('can:service.show')->only('show');
        $this->middleware('can:service.create')->only('create');
        $this->middleware('can:service.edit')->only('edit');
        $this->middleware('can:service.destroy')->only('destroy');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $services = Service::query()
            ->select('services.id', 'services.description as service', 'stores.description as store', 'services.service_phone','services.service_ip', 'services.service_extension','services.created_at', 'services.updated_at','stores.description')
            ->join('stores', 'stores.id', '=', 'services.store_id')
            ->orderBy('services.id')
            ->when(Peticion::input('search'), function ($query, $search) {
                $query->where('services.description', 'like', '%' . $search . '%')
                    ->OrWhere('services.service_extension', 'like', '%' . $search . '%');
            })->paginate(25);

        return Inertia::render('Service/Index', compact('services'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $stores = Store::select('id','description')->orderBy('id')->get();

        return Inertia::render('Service/Create', compact('stores'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $service = new Service();
        $service->description = $request->description;
        $service->service_phone = $request->service_phone;
        $service->service_extension = $request->service_extension;
        $service->service_ip = $request->service_ip;
        $service->store_id = $request->store_id;
        $service->save();

        return redirect()->route('service.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $service = Service::findorfail($id);
        $stores = Store::select('id', 'store_number','description')->orderBy('id')->get();
        return Inertia::render('Service/Edit', compact('service','stores'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $service = Service::findOrFail($id);
        $service->description = $request->description;
        $service->service_phone = $request->service_phone;
        $service->service_extension = $request->service_extension;
        $service->service_ip = $request->service_ip;
        $service->store_id = $request->store_id;
        $service->save();

        return redirect()->route('service.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $service = Service::findorfail($id);
        $service->delete();
        return redirect()->route('service.index');
    }
}
