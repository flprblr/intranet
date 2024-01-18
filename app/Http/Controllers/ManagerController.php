<?php

namespace App\Http\Controllers;

use App\Models\Store;
use App\Models\Manager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Request as Peticion;
use Inertia\Inertia;

class ManagerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $managers = Manager::query()
            ->select('managers.id','managers.name','managers.email as email_supervisor','stores.description', 'stores.email as email_tienda','stores.werks')
            ->join('stores', 'stores.id', '=', 'managers.store_id')
            ->orderBy('id')
            ->when(Peticion::input('search'), function ($query, $search) {
                $query->where('managers.name', 'like', '%' . $search . '%')
                    ->OrWhere('stores.description', 'like', '%' . $search . '%');
            })->paginate(25);

        return Inertia::render('Manager/Index', compact('managers'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $stores = Store::select('id', 'description','werks')->orderBy('id')->get();
        return Inertia::render('Manager/Create', compact('stores'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $manager = new Manager();
        $manager->name = $request->name;
        $manager->email = $request->email;
        $manager->store_id = $request->store_id;
        //$manager->werks = $request->werks;
        $manager->save();

        return redirect()->route('manager.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */   
    public function show($id) 
    {
        
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $manager = Manager::findorfail($id);
        
        $store = Store::select('stores.id','stores.description', 'stores.werks')
                 ->get();

                
        return Inertia::render('Manager/Edit', compact('store','manager'));
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
        $manager = Manager::findorfail($id);
        $manager->name = $request->name;
        $manager->email = $request->email;
        $manager->store_id = $request->store_id;
        $manager->save();

        return redirect()->route('manager.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $manager = Manager::findorfail($id);
        $manager->delete();
        return redirect()->route('manager.index');
    }
}
