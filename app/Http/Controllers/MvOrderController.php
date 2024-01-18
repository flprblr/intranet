<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MvOrder;
use App\Models\MvOrderDetail;
use App\Models\MvOrderPayment;
use App\Models\MvStore;
use Illuminate\Support\Facades\Request as Peticion;
use Inertia\Inertia;
use Illuminate\Support\Facades\Artisan;

class MvOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $search = Peticion::input('search');

        $mvOrders = MvOrder::select(
            'mv_orders.id',
            'mv_orders.prefix',
            'mv_stores.connection',
            'mv_warehouses.description',
            'mv_orders.order_number',
            'mv_orders.invoice_number',
            'mv_orders.invoice_url',
            'mv_orders.date_created',
            'mv_orders.total',
            'mv_orders.payment_status',
            'order_statuses.name',
            'mv_orders.billing_name',
            'mv_orders.billing_last_name',
            'mv_orders.billing_rut'
        )
            ->join('mv_stores', 'mv_orders.store_id', '=', 'mv_stores.id')
            ->join('mv_warehouses', 'mv_orders.id_warehouse', '=', 'mv_warehouses.id')
            ->join('order_statuses', 'mv_orders.order_status_id', '=', 'order_statuses.id')
            ->when($search, function ($query) use ($search) {
                $query->where('mv_orders.prefix', 'like', '%' . $search . '%')
                    ->orWhere('mv_orders.order_number', 'like', '%' . $search . '%')
                    ->orWhere('mv_orders.invoice_number', 'like', '%' . $search . '%')
                    ->orWhere('mv_orders.date_created', 'like', '%' . $search . '%')
                    ->orWhere('mv_orders.total', 'like', '%' . $search . '%')
                    ->orWhere('order_statuses.name', 'like', '%' . $search . '%')
                    ->orWhere('mv_orders.billing_name', 'like', '%' . $search . '%')
                    ->orWhere('mv_orders.billing_last_name', 'like', '%' . $search . '%')
                    ->orWhere('mv_orders.billing_rut', 'like', '%' . $search . '%')
                    ->orWhere('mv_stores.connection', 'like', '%' . $search . '%');
            })
            ->orderByDesc('id')
            ->paginate(25);

        return Inertia::render('MvOrder/Index', compact('mvOrders'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $stores = MvStore::select('id', 'connection', 'marketplace_id')
            ->where('active', 1)
            ->orderBy('id')
            ->get();

        return Inertia::render('MvOrder/Create', compact('stores'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Obtener los datos enviados desde el formulario Vue
        $id = $request->input('id');
        $order_number = $request->input('order_number');
        $startDate = $request->input('startDate');
        $endDate = $request->input('endDate');

        // Ejecutar tu comando de Laravel con los datos recibidos
        Artisan::call('get:mvordersmanual', [
            'id' => $id,
            'order_number' => $order_number,
            'startDate' => $startDate,
            'endDate' => $endDate,
        ]);

        return redirect()->route('mvorder.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\MvOrder  $mvOrder
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $mvOrders = MvOrder::query()
            ->select(
                'mv_orders.id',
                'mv_orders.prefix',
                'mv_orders.order_number',
                'mv_orders.invoice_number',
                'mv_orders.invoice_url',
                'mv_orders.date_created',
                'mv_orders.net',
                'mv_orders.tax',
                'mv_orders.total',
                'mv_orders.payment_status',
                'order_statuses.name',
                'mv_orders.billing_name',
                'mv_orders.billing_last_name',
                'mv_orders.billing_rut',
                'mv_orders.billing_state',
                'mv_orders.billing_city',
                'mv_orders.billing_address_1',
                'mv_warehouses.description',
                'mv_warehouses.logo',
                'mv_stores.connection'
            )
            ->join('order_statuses', 'mv_orders.order_status_id', '=', 'order_statuses.id')
            ->join('mv_warehouses', 'mv_orders.id_warehouse', '=', 'mv_warehouses.id')
            ->join('mv_stores', 'mv_orders.store_id', '=', 'mv_stores.id')
            ->where('mv_orders.id', '=', $id)
            ->paginate(25);

        $rows = MvOrderDetail::where('order_id', $id)->get();

        $mvPayments = MvOrderPayment::where('order_id', $id)->get();


        return Inertia::render('MvOrder/Show', compact('mvOrders', 'rows', 'mvPayments'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
