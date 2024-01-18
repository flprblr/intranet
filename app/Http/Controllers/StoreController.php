<?php

namespace App\Http\Controllers;

use App\Models\Store;
use App\Models\Station;
use App\Models\Service;
use App\Models\Manager;
use App\Models\State;
use App\Models\City;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Request as Peticion;
use Inertia\Inertia;


class StoreController extends Controller
{

    public function __construct() {
        $this->middleware('can:store.index')->only('index');
        $this->middleware('can:store.show')->only('show');
        $this->middleware('can:store.create')->only('create');
        $this->middleware('can:store.edit')->only('edit');
        $this->middleware('can:store.destroy')->only('destroy');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $stores = Store::query()
            ->select('stores.id',
                     'stores.store_number',
                     'stores.description',
                     'stores.extension', 
                     'stores.phone_number', 
                     'stores.gateway_ip',
                     'stores.router_ip',
                     'stores.phone_ip',
                     'stores.clock_ip',
                     'stores.clock_model',
                     'stores.wifi_password',
                     'stores.service_code',
                     'stores.address',
                     'stores.email',
                     'stores.condition',
                     'states.name as state',
                     'cities.name as city',
                     'managers.name as manager',
                     'managers.email as manager_email')
            ->join('states','stores.state_id','=','states.id')
            ->join('cities','stores.city_id','=','cities.id')
            ->leftjoin('managers','managers.store_id','=','stores.id')
            ->orderBy('stores.id')
            ->when(Peticion::input('search'), function ($query, $search) {
                $query->where('store_number', 'like', '%' . $search . '%')
                    ->OrWhere('description', 'like', '%' . $search . '%')
                    ->OrWhere('extension', 'like', '%' . $search . '%')
                    ->OrWhere('address', 'like', '%' . $search . '%')
                    ->OrWhere('werks', 'like', '%' . $search . '%')
                    ->OrWhere('service_code', 'like', '%' . $search . '%');
            })->paginate(25);


        return Inertia::render('Store/Index', compact('stores'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {   
        $states = State::select('id', 'name','number')->orderBy('id')->get();
        $cities = City::select('id','name','state_id')->orderby('name')->get();
        return Inertia::render('Store/Create', compact('states','cities'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $store = new Store();
        $store->store_number = $request->store_number;
        $store->description = $request->description;
        $store->werks = $request->werks;
        $store->extension = $request->extension;
        $store->phone_number = $request->phone_number;
        $store->gateway_ip = $request->gateway_ip;
        $store->router_ip = $request->router_ip;
        $store->phone_ip = $request->phone_ip;
        $store->clock_ip = $request->clock_ip;
        $store->clock_model = $request->clock_model;
        $store->wifi_password = $request->wifi_password;
        $store->service_code = $request->service_code;
        $store->address = $request->address;
        $store->email = $request->email;
        $store->condition = $request->condition;
        $store->city_id = $request->city_id;
        $store->state_id = $request->state_id;
        $store->save();

        return redirect()->route('store.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {   
        $store = Store::findorfail($id);

        $stations = Station::select('stations.id','stations.station_number','stations.station_ip')
                    ->join('stores','stations.store_id','=','stores.id')
                    ->where('stations.store_id','=',$id)
                    ->get();

        $services = Service::select('services.description as service','services.service_extension','services.service_ip','service_phone')
                    ->join('stores','services.store_id','=','stores.id')
                    ->where('services.store_id','=',$id)
                    ->get();


        $stores = Store::select('states.name as s_name', 'cities.name as c_name')
                    ->join('states','stores.state_id','=','states.id')
                    ->join('cities','stores.city_id','=','cities.id')
                    ->where('stores.id','=',$id)
                    ->get();

        $manager = Manager::select('managers.name as manager', 'managers.email as manager_email')
                    ->join('stores','managers.store_id','=','stores.id')
                    ->where('managers.store_id','=',$id)
                    ->get();

        $cities = City::select('id','name','state_id')->orderby('name')->get();
        $states = State::select('id', 'name','number')->orderBy('id')->get();
                
        return Inertia::render('Store/Show', compact('stations','store','stores','cities','states','services','manager'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $store = Store::findorfail($id);
        $states = State::select('id', 'name','number')->orderBy('id')->get();
        $cities = City::select('id','name','state_id')->orderby('name')->get();
        $stations = Station::select('stations.id','stations.station_number','stations.station_ip')
                    ->join('stores','stations.store_id','=','stores.id')
                    ->where('stations.store_id','=',$id)
                    ->get();
        $services = Service::select('services.description as service','services.service_extension','services.service_ip','service_phone')
                    ->join('stores','services.store_id','=','stores.id')
                    ->where('services.store_id','=',$id)
                    ->get();
        return Inertia::render('Store/Edit', compact('store','states','cities','stations','services'));

        
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
        $store = Store::findorfail($id);
        $store->store_number = $request->store_number;
        $store->description = $request->description;
        $store->werks = $request->werks;
        $store->extension = $request->extension;
        $store->phone_number = $request->phone_number;
        $store->gateway_ip = $request->gateway_ip;
        $store->router_ip = $request->router_ip;
        $store->phone_ip = $request->phone_ip;
        $store->clock_ip = $request->clock_ip;
        $store->clock_model = $request->clock_model;
        $store->wifi_password = $request->wifi_password;
        $store->service_code = $request->service_code;
        $store->address = $request->address;
        $store->email = $request->email;
        $store->condition = $request->condition;
        $store->city_id = $request->city_id;
        $store->state_id = $request->state_id;
        $store->save();

        return redirect()->route('store.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $store = Store::findorfail($id);
        $store->delete();
        return redirect()->route('store.index');
    }
}
