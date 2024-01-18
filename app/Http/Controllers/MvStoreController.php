<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MvStore;
use App\Models\MvMarketplace;
use Inertia\Inertia;
use Illuminate\Support\Facades\Request as Peticion;

class MvStoreController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $mvStores = MvStore::select('mv_stores.id', 'mv_stores.connection', 'mv_marketplaces.marketplace', 'mv_stores.active')
            ->join('mv_marketplaces', 'mv_stores.marketplace_id', '=', 'mv_marketplaces.id')
            ->orderBy('mv_stores.id')
            ->when(Peticion::input('search'), function ($query, $search) {
                $query->where('mv_stores.connection', 'like', '%' . $search . '%')
                    ->orWhere('mv_marketplaces.marketplace', 'like', '%' . $search . '%');
            })->paginate(50);

        return Inertia::render('MvStore/Index', compact('mvStores'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $marketplaces = MvMarketplace::select('id', 'marketplace')->orderBy('id')->get();

        return Inertia::render('MvStore/Create', compact('marketplaces'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $mvStores = new MvStore();
        $mvStores->id = $request->id;
        $mvStores->connection = $request->connection;
        $mvStores->marketplace_id = $request->marketplace_id;
        $mvStores->active = $request->active;
        $mvStores->save();

        return redirect()->route('mvstore.index');
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
        $mvStores = MvStore::findorfail($id);
        $marketplaces = MvMarketplace::select('id', 'marketplace')->orderBy('id')->get();
        return Inertia::render('MvStore/Edit', compact('mvStores', 'marketplaces'));
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
        $mvStores = MvStore::findOrFail($id);

        $mvStores->connection = $request->connection;
        $mvStores->marketplace_id = $request->marketplace_id; // Asegúrate de que marketplace_id se reciba correctamente
        $mvStores->active = $request->active;

        $mvStores->save();

        return redirect()->route('mvstore.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $mvStores = MvStore::findOrFail($id);
        $mvStores->delete();

        return redirect()->route('mvstore.index');
    }
}
