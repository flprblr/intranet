<?php

namespace App\Http\Controllers;

use App\Models\State;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Request as Peticion;
use Inertia\Inertia;

class StateController extends Controller
{

    public function __construct() {
        $this->middleware('can:state.index')->only('index');
        $this->middleware('can:state.show')->only('show');
        $this->middleware('can:state.create')->only('create');
        $this->middleware('can:state.edit')->only('edit');
        $this->middleware('can:state.destroy')->only('destroy');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        
        $states = State::query()
        ->select('id', 'name','number', 'created_at', 'updated_at')
        ->orderBy('id')
        ->when(Peticion::input('search'), function ($query, $search) {
            $query->where('id', 'like', '%' . $search . '%')
                ->OrWhere('name', 'like', '%' . $search . '%');
        })->paginate(25);

    return Inertia::render('State/Index', compact('states'));

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $state = State::select('id', 'name','number')->orderBy('id')->get();
        return Inertia::render('State/Create', compact('state'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        
        $state = new State;
        $state->name = $request->name;
        $state->number = $request->number;
        $state->save();

        return redirect()->route('state.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        
        $states = State::query()
            ->select('id', 'name', 'number', 'created_at', 'updated_at')
            ->where('id', $id)
            ->when(Peticion::input('search'), function ($query, $search) {
                $query->where('id', 'like', '%' . $search . '%')
                    ->OrWhere('name', 'like', '%' . $search . '%');
            })->paginate(25);

        return Inertia::render('State/Index', compact('states'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $state = State::findorfail($id);
        
        return Inertia::render('State/Edit', compact('state'));
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
        $state = State::findOrFail($id);
        $state->name = $request->name;
        $state->number = $request->number;
        $state->save();
        return redirect()->route('state.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $state = State::findOrFail($id);
        $state->delete();

        return redirect()->route('state.index');
    }
}
