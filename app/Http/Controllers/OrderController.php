<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Payment;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Request as Peticion;
use Inertia\Inertia;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $orders = Order::query()
            ->select(
                'orders.id',
                'orders.prefix',
                'orders.order_number',
                'orders.invoice_number',
                'orders.invoice_number_rev',
                'orders.invoice_url',
                'orders.invoice_url_rev',
                'orders.date_created',
                'orders.total',
                'order_statuses.name',
                'orders.billing_first_name',
                'orders.billing_last_name',
                'orders.billing_rut',
                'orders.billing_email',
                'orders.order_status_id'
            )
            ->join('order_statuses', 'orders.order_status_id', '=', 'order_statuses.id')
            ->when(Peticion::input('search'), function ($query, $search) {
                $query->where('orders.prefix', 'like', '%' . $search . '%')
                    ->OrWhere('orders.order_number', 'like', '%' . $search . '%')
                    ->OrWhere('orders.invoice_number', 'like', '%' . $search . '%')
                    ->OrWhere('orders.date_created', 'like', '%' . $search . '%')
                    ->OrWhere('orders.total', 'like', '%' . $search . '%')
                    ->OrWhere('order_statuses.name', 'like', '%' . $search . '%')
                    ->OrWhere('orders.billing_first_name', 'like', '%' . $search . '%')
                    ->OrWhere('orders.billing_last_name', 'like', '%' . $search . '%')
                    ->OrWhere('orders.billing_rut', 'like', '%' . $search . '%')
                    ->OrWhere('orders.billing_email', 'like', '%' . $search . '%');
            })
            ->orderByDesc('id')
            ->paginate(25);

        return Inertia::render('Order/Index', compact('orders'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function show(Order $order)
    {
        $orders = Order::query()
            ->select(
                'orders.id',
                'orders.prefix',
                'orders.order_number',
                'orders.invoice_number',
                'orders.invoice_number_rev',
                'orders.invoice_url',
                'orders.invoice_url_rev',
                'orders.date_created',
                'orders.discount_code',
                'orders.discount_type',
                'orders.discount_amount',
                'orders.discount_total',
                'orders.net',
                'orders.tax',
                'orders.total',
                'orders.billing_first_name',
                'orders.billing_last_name',
                'orders.billing_rut',
                'orders.billing_phone',
                'orders.billing_email',
                'orders.billing_country',
                'orders.billing_state',
                'orders.billing_city',
                'orders.billing_address_1',
                'order_statuses.name'
            )
            ->join('order_statuses', 'orders.order_status_id', '=', 'order_statuses.id')
            ->where('orders.id', '=', $order->id)
            ->paginate(25);

        $rows = OrderDetail::where('order_id', $order->id)->get();

        $payments = Payment::where('id', $order->payment_id)->get();


        return Inertia::render('Order/Show', compact('orders', 'rows', 'payments'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function edit(Order $order)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Order $order)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function destroy(Order $order)
    {
        //
    }
}
