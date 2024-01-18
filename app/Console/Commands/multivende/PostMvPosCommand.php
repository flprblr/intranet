<?php

namespace App\Console\Commands\multivende;

use Illuminate\Console\Command;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

use App\Models\MvOrder;
use App\Models\MvWarehouse;
use App\Models\MvMarketplace;
use App\Models\MvOrderDetail;
use App\Models\MvOrderPayment;

class PostMvPosCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'post:mvpos';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Post Multivende Orders to Pos';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $startTime = Carbon::now();

        try {
            $orders = MvOrder::with('warehouse.company')
                ->where('payment_status', 'completed')
                //->where('id', 19)
                ->whereIn('order_status_id', [5, 6, 7])
                ->orderBy('id')
                ->get();

            foreach ($orders as $order) {
                $company = $order->warehouse->company;
                $this->changeStatus($order->id, 6); // POS: Enviado
                $c_bpartner_id = $this->pos_insert_bpartner($order, $company);
                if ($c_bpartner_id === 0 || !$this->handleDocumento($order, $c_bpartner_id, $company)) {
                    continue;
                }

                $this->changeStatus($order->id, 8); // POS: Emitido
            }
        } catch (\Exception $e) {
            Log::channel('mv-pos')->error($e->getMessage());
        }

        $endTime = Carbon::now();
        $executionTime = $endTime->diffInSeconds($startTime);
        Log::channel('mv-pos')->error('POST POS Orders: ' . $executionTime . ' seconds');
        return Command::SUCCESS;
    }

    private function handleDocumento($order, $c_bpartner_id, $company)
    {
        $c_documento_id = $this->pos_insert_documento($c_bpartner_id, $order, $company);
        if ($c_documento_id === 0) {
            $this->changeStatus($order->id, 7); // POS: Error
            return false;
        }

        $orderDetails = MvOrderDetail::where('order_id', $order->id)->get();
        foreach ($orderDetails as $orderDetail) {
            $result = $this->pos_insert_detalle_documento($c_documento_id, $order, $orderDetail, $company);
            if ($result === 0) {
                $this->changeStatus($order->id, 7); // POS: Error
                $this->pos_delete_documento($c_documento_id, $company);
                Log::channel('mv-pos')->error('Post Order ERROR: ' . $order->order_number);
                return false;
            }
        }

        $orderPayments = MvOrderPayment::where('order_id', $order->id)->get();
        foreach ($orderPayments as $orderPayment) {
            $result = $this->pos_insert_pago($c_documento_id, $orderPayment, $company);
            if ($result === 0) {
                $this->changeStatus($order->id, 7); // POS: Error
                $this->pos_delete_documento($c_documento_id, $company);
                Log::channel('mv-pos')->error('Post Order to POS ERROR: ' . $order->order_number);
                return false;
            }
        }

        //Log::channel('mv-pos')->error('Post Order to POS: ' . $order->order_number);
        return true;
    }

    private function pos_insert_bpartner($order, $company)
    {
        $sql = "SELECT pos.f_pos_insert_bpartner_ccv2(?, ?, ?, ?, ?, ?, ?, ?)";
        $c_bpartner_id = 0;

        try {
            $result = DB::connection($company->server)->select($sql, [
                $order->billing_rut,
                $order->billing_name,
                $order->billing_last_name,
                $order->billing_email,
                $order->billing_address_1,
                $order->billing_city,
                $order->billing_state,
                $order->billing_phone
            ]);

            $c_bpartner_id = $result[0]->f_pos_insert_bpartner_ccv2;
        } catch (\Exception $e) {
            // Manejar la excepción según tus necesidades
            Log::channel('mv-pos')->error($e->getMessage());
        }

        return $c_bpartner_id;
    }

    private function pos_insert_documento($c_bpartner_id, $order, $company)
    {
        $warehouse = MvWarehouse::where('id', $order->id_warehouse)->first();
        $marketplace = MvMarketplace::where('id', $order->marketplace_id)->first();


        $sql = "SELECT pos.f_pos_insert_documento_mk(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $c_documento_id = 0;

        try {
            $result = DB::connection($company->server)->select($sql, [
                101, //f_tipo_documento integer,
                $order->invoice_number, //f_nro_documento character varying,
                $order->net, //f_neto integer,
                $order->tax, //f_iva integer,
                $order->date_created, //f_fecha_emision date,
                $warehouse->werks, //f_m_warehouse_id character varying,
                155, //f_cajero_id integer,
                $warehouse->lgort, //f_almacen character varying,
                $marketplace->auart, //f_order_class_s4 character varying,
                $order->order_number, //f_nro_consigment character varying,
                $warehouse->vkorg, //f_orgvta character varying,
                $company->rut, //f_rut_empresa character varying, 
                $c_bpartner_id //f_bpartner_id integer
            ]);

            $c_documento_id = $result[0]->f_pos_insert_documento_mk;
        } catch (\Exception $e) {
            // Manejar la excepción según tus necesidades
            Log::channel('mv-pos')->error($e->getMessage());
        }

        return $c_documento_id;
    }

    private function pos_insert_detalle_documento($c_documento_id, $order, $orderDetail, $company)
    {
        $warehouse = MvWarehouse::where('id', $order->id_warehouse)->first();

        $sql = "SELECT pos.f_pos_insert_detalle_documento_mv(?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $resultado = 0;

        try {
            $result = DB::connection($company->server)->select($sql, [
                $c_documento_id, //f_c_documento_id integer,
                155, //f_vendedor character varying,
                $orderDetail->sku, //f_upc character varying,
                $orderDetail->quantity, //f_cantidad_linea integer,
                $orderDetail->unit_price, //f_precio_lista integer,
                $orderDetail->discount, //f_valor_descuento integer,
                $orderDetail->final_price, //f_total_linea integer,
                $warehouse->werks, //f_m_warehouse_id character varying,
                $orderDetail->description //f_nombre_producto character varying
            ]);

            $resultado = $result[0]->f_pos_insert_detalle_documento_mv;
        } catch (\Exception $e) {
            // Manejar la excepción según tus necesidades
            Log::channel('mv-pos')->error($e->getMessage());
        }

        return $resultado;
    }

    private function pos_insert_pago($c_documento_id, $orderPayment, $company)
    {
        $sql = "SELECT pos.f_pos_insert_pagos_mv(?, ?, ?, ?, ?)";
        $c_pagos_id = 0;

        try {
            $result = DB::connection($company->server)->select($sql, [
                $c_documento_id, //f_c_documento_id integer,
                $orderPayment->amount, //f_monto integer,
                $orderPayment->date, //f_fecha_emision date,
                $orderPayment->pos_id, //f_tipo_pago integer,
                0 //f_tarjeta_id integer
            ]);

            $c_pagos_id = $result[0]->f_pos_insert_pagos_mv;
        } catch (\Exception $e) {
            // Manejar la excepción según tus necesidades
            Log::channel('mv-pos')->error($e->getMessage());
        }

        return $c_pagos_id;
    }

    private function pos_delete_documento($c_documento_id, $company)
    {
        $sql = "SELECT pos.ynk_borra_boleta(?)";
        $resultado = 0;

        try {
            $result = DB::connection($company->server)->select($sql, [
                $c_documento_id, //f_c_documento_id integer,
            ]);

            $resultado = $result[0]->f_pos_insert_pagos;
        } catch (\Exception $e) {
            // Manejar la excepción según tus necesidades
            Log::channel('mv-pos')->error($e->getMessage());
        }

        return $resultado;
    }

    private function changeStatus($orderId, $statusId)
    {
        $order = MvOrder::find($orderId);
        $order->order_status_id = $statusId;
        $order->save();
    }
}
