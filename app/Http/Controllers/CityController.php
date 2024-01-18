<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\State;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Request as Peticion;
use Inertia\Inertia;

class CityController extends Controller
{

    public function __construct() {
        $this->middleware('can:city.index')->only('index');
        $this->middleware('can:city.show')->only('show');
        $this->middleware('can:city.create')->only('create');
        $this->middleware('can:city.edit')->only('edit');
        $this->middleware('can:city.destroy')->only('destroy');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $cities = City::query()
            ->select('cities.id', 'cities.name','states.name as state_name', 'cities.created_at', 'cities.updated_at')
            ->join('states', 'states.id', '=', 'cities.state_id')
            ->orderBy('cities.id')
            ->when(Peticion::input('search'), function ($query, $search) {
                $query->where('cities.name', 'like', '%' . $search . '%')
                    ->OrWhere('states.name', 'like', '%' . $search . '%');
            })->paginate(50);

        return Inertia::render('City/Index', compact('cities'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $states = State::select('id', 'name','number')->orderBy('id')->get();
        return Inertia::render('City/Create', compact('states'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $city = new City();
        $city->name = $request->name;
        $city->state_id = $request->state_id;
        $city->save();

        return redirect()->route('city.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $cities = City::query()
        ->select('cities.id', 'cities.name','states.name as state_name', 'cities.created_at', 'cities.updated_at')
        ->join('states', 'states.id', '=', 'cities.state_id')
        ->when(Peticion::input('search'), function ($query, $search) {
            $query->where('cities.name', 'like', '%' . $search . '%')
                ->OrWhere('state.name', 'like', '%' . $search . '%');
        })->paginate(1);

    return Inertia::render('City/Index', compact('cities'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $city = City::findorfail($id);
        $states = State::select('id', 'name','number')->orderBy('id')->get();
        return Inertia::render('City/Edit', compact('city','states'));
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
        $city = City::findOrFail($id);
        $city->name = $request->name;
        $city->state_id = $request->state_id;
        $city->save();
        return redirect()->route('city.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $city = City::findOrFail($id);
        $city->delete();

        return redirect()->route('city.index');
    }
}
