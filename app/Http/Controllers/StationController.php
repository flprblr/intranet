<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Station;
use App\Models\Store;
use Inertia\Inertia;
use Illuminate\Support\Facades\Request as Peticion;

class StationController extends Controller
{   

    public function __construct() {
        $this->middleware('can:station.index')->only('index');
        $this->middleware('can:station.show')->only('show');
        $this->middleware('can:station.create')->only('create');
        $this->middleware('can:station.edit')->only('edit');
        $this->middleware('can:station.destroy')->only('destroy');
    } /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $stations = Station::query()
            ->select('stations.id', 'stations.station_number','stations.station_ip','stations.created_at', 'stations.updated_at','stores.description')
            ->join('stores', 'stores.id', '=', 'stations.store_id')
            ->orderBy('stations.id')
            ->when(Peticion::input('search'), function ($query, $search) {
                $query->where('stations.station_number', 'like', '%' . $search . '%')
                    ->OrWhere('stations.station_ip', 'like', '%' . $search . '%');
            })->paginate(25);

        return Inertia::render('Station/Index', compact('stations'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $stores = Store::select('id','description')->orderBy('id')->get();

        return Inertia::render('Station/Create', compact('stores'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $station = new Station();
        $station->station_number = $request->station_number;
        $station->station_ip = $request->station_ip;
        $station->store_id = $request->store_id;
        $station->save();

        return redirect()->route('station.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $stations = Station::query()
        ->select('stations.id', 'stations.station_number','stations.station_ip','stations.created_at', 'stations.updated_at','stores.description')
        ->join('stores', 'stores.id', '=', 'stations.store_id')
        ->when(Peticion::input('search'), function ($query, $search) {
            $query->where('stations.station_number', 'like', '%' . $search . '%')
                ->OrWhere('stations.station_ip', 'like', '%' . $search . '%');
        })->paginate(1);

        return Inertia::render('Station/Index', compact('stations'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $station = Station::findorfail($id);
        $stores = Store::select('id', 'store_number','description')->orderBy('id')->get();
        return Inertia::render('Station/Edit', compact('station','stores'));
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
        $station = Station::findOrFail($id);
        $station->station_number = $request->station_number;
        $station->station_ip = $request->station_ip;
        $station->store_id = $request->store_id;
        $station->save();
        return redirect()->route('station.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $station = Station::findorfail($id);
        $station->delete();
        return redirect()->route('station.index');
    }

}
