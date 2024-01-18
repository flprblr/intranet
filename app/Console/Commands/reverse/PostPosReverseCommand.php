<?php

namespace App\Console\Commands\reverse;

use Illuminate\Console\Command;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

use App\Models\Company;
use App\Models\Ecommerce;
use App\Models\Payment;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\OrderStatus;

use GuzzleHttp\Client;
use Automattic\WooCommerce\Client as WooClient;
use Illuminate\Support\Facades\Config;

class PostPosReverseCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'post:posReverse';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Post E-Commerce Orders Reverse to Pos';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $integration_mode = Config::get('app.INTEGRATION_MODE');

        $startTime = Carbon::now();

        try {

            $ecommerces = Ecommerce::select('id', 'prefix', 'api_key', 'api_secret', 'url', 'logo', 'VKORG', 'WERKS', 'LGORT', 'AUART', 'FKART', 'company_id')->get();

            foreach ($ecommerces as $ecommerce) {

                $company = Company::select('id', 'name', 'rut', 'city', 'commune', 'address', 'activity', 'acteco', 'sovos_user', 'sovos_password', 'server')
                    ->where('id', $ecommerce->company_id)
                    ->first();

                $orders = Order::select('id', 'prefix', 'order_number', 'invoice_number_rev', 'invoice_url', 'date_created', 'date_modified', 'discount_code', 'discount_type', 'discount_amount', 'discount_total', 'shipping_total', 'net', 'tax', 'total', 'billing_first_name', 'billing_last_name', 'billing_rut', 'billing_company', 'billing_phone', 'billing_email', 'billing_country', 'billing_state', 'billing_city', 'billing_address_1', 'order_status_id', 'payment_id', 'ecommerce_id', 'created_at', 'updated_at')
                    ->where(function ($query) {
                        $query->where('order_status_id', 21)  //Sovos: Reverse Complete
                            ->orWhere('order_status_id', 25);  //POS: Error Reverse     
                    })
                    ->where('ecommerce_id', $ecommerce->id)
                    ->whereNotNull('invoice_number_rev')
                    ->get();

                foreach ($orders as $order) {

                    $payment = Payment::where('id', $order->payment_id)->first();

                    $frame = $this->prepareFrame($ecommerce, $company, $order, $payment);
                    #Log::info($frame);
                    $xml = $this->prepareXml($frame);
                    $this->sendToPos($integration_mode, $company,  $xml, $order, $ecommerce);
                }
            }
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }

        $endTime = Carbon::now();
        $executionTime = $endTime->diffInSeconds($startTime);
        Log::info('Post Orders Reverse ->Pos: ' . $executionTime . ' seconds');

        return Command::SUCCESS;
    }

    private function prepareFrame($ecommerce, $company, $order, $payment)
    {
        $separator = "\n";
        $frame = "<arg0>
                        <tipo_documento>104</tipo_documento>
                        <cajero_id>155</cajero_id>
                        <orgVta>" . $ecommerce->VKORG . "</orgVta>
                        <m_warehouse_id>" . $ecommerce->WERKS . "</m_warehouse_id>
                        <almacen>" . $ecommerce->LGORT . "</almacen>
                        <orderClass>" . $ecommerce->AUART . "</orderClass>
                        <invoiceClass>" . $ecommerce->FKART . "</invoiceClass>
                        <nro_consigment>" . $order->prefix . "-" . $order->order_number . "</nro_consigment>
                        <nro_documento>" . $order->invoice_number_rev . "</nro_documento>
                        <fecha_emision>" . date('d/m/Y', strtotime($order->date_created)) . "</fecha_emision>
                        <neto>" . $order->net . "</neto>
                        <iva>" . $order->tax . "</iva>
                        <nombre_cliente>" . $order->billing_first_name . "</nombre_cliente>
                        <apellido_cliente>" . $order->billing_last_name . "</apellido_cliente>
                        <rut_cliente>" . $order->billing_rut . "</rut_cliente>
                        <telefono_cliente>" . $order->billing_phone . "</telefono_cliente>
                        <email_cliente>" . $order->billing_email . "</email_cliente>
                        <ciudad_cliente>" . $order->billing_state . "</ciudad_cliente>
                        <comuna_cliente>" . $order->billing_city . "</comuna_cliente>
                        <direccion_cliente>" .  str_replace('&', '', substr($order->billing_address_1, 0, 100)) . "</direccion_cliente>
                    </arg0>";
        $frame .= $separator;

        $orderDetails = OrderDetail::select('id', 'sku', 'description', 'unit_price', 'quantity', 'subtotal', 'final_price', 'discount', 'total', 'order_id')
            ->where('order_id', $order->id)->get();

        foreach ($orderDetails as $orderLine) {
            $frame .= "<arg1>
                                <sku>" . $orderLine->sku . "</sku>
                                <nombre_producto>" . str_replace('&', '', $orderLine->description) . "</nombre_producto>
                                <precio_lista>" . $orderLine->unit_price . "</precio_lista>
                                <cantidad_linea>" . $orderLine->quantity . "</cantidad_linea>
                                <valor_descuento>" . $orderLine->discount . "</valor_descuento>
                                <total_linea>" . $orderLine->total . "</total_linea>
                                <vendedor_id>155</vendedor_id>
                                <nro_serie_apple></nro_serie_apple>
                            </arg1>";
            $frame .= $separator;
        }

        if ($payment->method == 'Webpay Plus' && $payment->type == 'Crédito') {
            $frame .= "<arg2>
                                <monto_pago>" . $order->total . "</monto_pago>
                                <tarjeta_id>1</tarjeta_id>
                                <tipo_pago>3</tipo_pago>
                            </arg2>";
        } elseif ($payment->method == 'Webpay Plus' && $payment->type == 'Débito') {
            $frame .= "<arg2>
                                <monto_pago>" . $order->total . "</monto_pago>
                                <tarjeta_id>2</tarjeta_id>
                                <tipo_pago>3</tipo_pago>
                            </arg2>";
        } else {
            $frame .= "<arg2>
                                <monto_pago>" . $order->total . "</monto_pago>
                                <tarjeta_id>0</tarjeta_id>
                                <tipo_pago>110</tipo_pago>
                            </arg2>";
        }
        Log::info($frame);
        return $frame;
    }

    private function prepareXml($frame)
    {
        $xml = "<soapenv:Envelope xmlns:soapenv='http://schemas.xmlsoap.org/soap/envelope/' xmlns:bel='http://belsport.cl/'>
                    <soapenv:Header/>
                    <soapenv:Body>
                        <bel:carga>
                            " . $frame . "
                        </bel:carga>
                    </soapenv:Body>
                </soapenv:Envelope>";
        return $xml;
    }

    private function sendToPos($integration_mode, $company,  $xml, $order, $ecommerce)
    {
        if ($company->server === 'bels') {
            $pos_prd = 'http://192.168.18.116:9090/WS/Boleta?wsdl';
            $pos_qas = 'http://10.16.3.210:9090/WS/Boleta_bels_qas?wsdl';
        } else {
            $pos_prd = 'http://192.168.18.118:9090/WS/BoletaIGSPRD?wsdl';
            $pos_qas = 'http://10.16.3.210:9080/WS/Boleta_igs_qas?wsdl';
        }

        $url = ($integration_mode === 'PRD') ? $pos_prd : $pos_qas;

        $client = new Client([
            'timeout' => 10,
            'headers' => [
                'Content-Type' => 'text/xml; charset=UTF-8',
            ],
        ]);

        $response = $client->post($url, ['body' => $xml]);

        $this->changeStatus($order->id, 24, $ecommerce); // POS: Enviado

        $response = $response->getBody()->getContents();
        $response = $this->everything_in_tags($response);

        if ($response === 'OK') {
            $this->changeStatus($order->id, 26, $ecommerce); // POS: Emitido
        } else {
            if ($this->checkInvoice($company, $order->invoice_number)) {
                $this->changeStatus($order->id, 26, $ecommerce); // POS: Emitido
            } else {
                $this->changeStatus($order->id, 25, $ecommerce); // POS: Error
            }
        }
    }

    private function changeStatus($orderId, $statusId, $ecommerce)
    {
        $order = Order::find($orderId);
        $order->order_status_id = $statusId;
        $order->save();

        /* $status = OrderStatus::where('id', $statusId)->pluck('slug')->first();

        $woocommerce = new WooClient(
            $ecommerce->url,
            $ecommerce->api_key,
            $ecommerce->api_secret,
            [
                'wp_api' => true,
                'version' => 'wc/v3',
                'timeout' => 10
            ]
        );

        $woocommerce->put("orders/{$order->order_number}", [
            'status' => $status
        ]);*/
    }

    private function everything_in_tags($string)
    {
        $pattern = "/<return>(.*?)<\/return>/s";
        preg_match($pattern, $string, $matches);
        return $matches[1];
    }

    private function checkInvoice($company, $invoice_number)
    {
        $result = DB::connection($company->server)->select("SELECT COUNT(*) FROM pos.pos_c_documento WHERE nro_documento = '$invoice_number'");
        if ($result >= 1) {
            return true;
        } else {
            return false;
        }
    }
}
