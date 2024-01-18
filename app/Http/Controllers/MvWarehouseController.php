<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Models\MvWarehouse;
use App\Models\Company;
use Illuminate\Support\Facades\Request as Peticion;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Artisan;

class MvWarehouseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $mvWarehouses = MvWarehouse::join('companies as b', 'mv_warehouses.company_id', '=', 'b.id')
            ->select(
                'mv_warehouses.id',
                'mv_warehouses.id_warehouse',
                'mv_warehouses.description',
                'mv_warehouses.vkorg',
                'mv_warehouses.werks',
                'mv_warehouses.lgort',
                'mv_warehouses.vtweg',
                'mv_warehouses.des_sovos',
                'mv_warehouses.logo',
                'b.name',
                'mv_warehouses.active'
            )
            ->orderBy('mv_warehouses.id')
            ->when(Peticion::input('search'), function ($query, $search) {
                $query->where('mv_warehouses.description', 'like', '%' . $search . '%');
            })->paginate(50);

        return Inertia::render('MvWarehouse/Index', compact('mvWarehouses'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        Artisan::call('get:mvwarehouses');

        $warehouseData = Cache::get('warehouse_data');

        if ($warehouseData) {
            foreach ($warehouseData as $data) {
                $existingWarehouse = MvWarehouse::where('id_warehouse', $data['id'])->first();

                if (!$existingWarehouse) {
                    MvWarehouse::create([
                        'id_warehouse' => $data['id'],
                        'description' => $data['name'],
                        'vkorg' => '1000',
                        'werks' => 'A000',
                        'lgort' => 'MP01',
                        'vtweg' => '05',
                        'des_sovos' => 'Descripcion',
                        'logo' => 'logo',
                        'company_id' => '1',
                        'active' => false,
                    ]);
                } else {
                }
            }
            return redirect()->back()->with('success', 'Datos de almacén obtenidos de la caché exitosamente');
        } else {
            // No se encontraron datos en la caché, maneja el error
            return redirect()->back()->with('error', 'No se pudieron obtener los datos del almacén de la caché');
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $mvWarehouses = new MvWarehouse();
        $mvWarehouses->id = $request->id;
        $mvWarehouses->id_warehouse = $request->id_warehouse;
        $mvWarehouses->vkorg = $request->vkorg;
        $mvWarehouses->werks = $request->werks;
        $mvWarehouses->lgort = $request->lgort;
        $mvWarehouses->vtweg = $request->vtweg;
        $mvWarehouses->des_sovos = $request->des_sovos;
        $mvWarehouses->logo = $request->logo;
        $mvWarehouses->name = $request->name;
        $mvWarehouses->active = $request->active;
        $mvWarehouses->save();

        return redirect()->route('mvwarehouse.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $mvWarehouses = MvWarehouse::join('companies as b', 'mv_warehouses.company_id', '=', 'b.id')
            ->select(
                'mv_warehouses.id',
                'mv_warehouses.id_warehouse',
                'mv_warehouses.description',
                'mv_warehouses.vkorg',
                'mv_warehouses.werks',
                'mv_warehouses.lgort',
                'mv_warehouses.vtweg',
                'mv_warehouses.des_sovos',
                'mv_warehouses.logo',
                'b.name',
                'mv_warehouses.active'
            )
            ->orderBy('mv_warehouses.id')
            ->when(Peticion::input('search'), function ($query, $search) {
                $query->where('mv_warehouses.description', 'like', '%' . $search . '%');
            })->paginate(1);

        return Inertia::render('MvWarehouse/Index', compact('mvWarehouses'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $mvWarehouses = MvWarehouse::findorfail($id);
        $companies = Company::select('id', 'name')->orderBy('id')->get();
        return Inertia::render('MvWarehouse/Edit', compact('mvWarehouses', 'companies'));
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
        $mvWarehouses = MvWarehouse::findOrFail($id);
        $mvWarehouses->id_warehouse = $request->id_warehouse;
        $mvWarehouses->vkorg = $request->vkorg;
        $mvWarehouses->werks = $request->werks;
        $mvWarehouses->lgort = $request->lgort;
        $mvWarehouses->vtweg = $request->vtweg;
        $mvWarehouses->des_sovos = $request->des_sovos;
        $mvWarehouses->logo = $request->logo;
        $mvWarehouses->company_id = $request->company_id;
        $mvWarehouses->active = $request->active;
        $mvWarehouses->save();
        return redirect()->route('mvwarehouse.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $mvWarehouses = MvWarehouse::findOrFail($id);
        $mvWarehouses->delete();

        return redirect()->route('mvwarehouse.index');
    }
}
