<?php

namespace App\Console\Commands;

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


class PostSovosCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'post:sovos';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Post E-Commerce Orders to Sovos';

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

            $ecommerces = Ecommerce::select('id', 'prefix', 'url', 'api_key', 'api_secret', 'logo', 'company_id')->get();

            foreach ($ecommerces as $ecommerce) {

                $company = Company::select('id', 'name', 'rut', 'city', 'commune', 'address', 'activity', 'acteco', 'sovos_user', 'sovos_password', 'server')
                    ->where('id', $ecommerce->company_id)
                    ->first();

                $orders = Order::select('id', 'order_number', 'invoice_number', 'date_created', 'net', 'tax', 'total', 'billing_first_name', 'billing_last_name', 'billing_rut', 'billing_phone', 'billing_email', 'billing_state', 'billing_city', 'billing_address_1', 'order_status_id')
                    ->where(function ($query) {
                        $query->where('order_status_id', 1)
                            ->orWhere('order_status_id', 2);
                    })
                    ->where('ecommerce_id', $ecommerce->id)
                    ->whereNull('invoice_number')
                    ->get();

                foreach ($orders as $order) {
                    if ($this->checkOrder($company, $order, $ecommerce)) {
                        $frame = $this->OnlineGenerationDteFrame($ecommerce, $company, $order);
                        $xml = $this->OnlineGenerationDteXml($company, $frame);
                        $this->OnlineGenerationDte($integration_mode, $xml, $order->id, $ecommerce);
                    }
                }
            }
        } catch (\Exception $e) {
            Log::channel('sovos')->error($e->getMessage());
        }

        $endTime = Carbon::now();
        $executionTime = $endTime->diffInSeconds($startTime);
        Log::channel('sovos')->info('POST Sovos Orders: ' . $executionTime . ' seconds');

        return Command::SUCCESS;
    }

    private function checkOrder($company, $order, $ecommerce)
    {
        $orderDetails = OrderDetail::select('sku')->where('order_id', $order->id)->get();

        foreach ($orderDetails as $orderLine) {
            $result = DB::connection($company->server)->select("SELECT COUNT(*) FROM pos.pos_m_producto WHERE sku = '$orderLine->sku' or upc = '$orderLine->sku'");
            $count = isset($result[0]->count) ? $result[0]->count : 0;
            if ($count === 0) {
                $this->changeStatus($order->id, 2, $ecommerce); // POS: Error Producto
                break;
                return false;
            }
        }
        return true;
    }

    private function OnlineGenerationDteFrame($ecommerce, $company, $order)
    {
        $separator = "\n";
        $frame = "FH|39||$order->date_created||||||$company->rut|$ecommerce->logo|$company->activity|$company->acteco|||$company->address|$company->commune|$company->city||$order->billing_rut|$order->billing_first_name $order->billing_last_name||$order->billing_address_1|$order->billing_city|$order->billing_state|$order->billing_address_1|$order->billing_city|$order->billing_state|$order->net|||$order->tax|$order->total||$order->total";
        $frame .= $separator;

        $orderDetails = OrderDetail::select('id', 'sku', 'description', 'unit_price', 'quantity', 'subtotal', 'final_price', 'discount', 'total', 'order_id')
            ->where('order_id', $order->id)->get();

        foreach ($orderDetails as $index => $orderLine) {
            $row = intval($index) + 1;
            $frame .= "DH|$row|0|" . str_replace('&', '', substr($orderLine->description, 0, 36))  . "||$orderLine->quantity|$orderLine->final_price|$orderLine->total|";
            $frame .= $separator;
            $frame .= "DHCD|$orderLine->sku|$orderLine->sku|";
            $frame .= $separator;
        }

        $frame .= "PE|$order->order_number|$order->billing_email|||$order->billing_phone|$ecommerce->url";

        return $frame;
    }

    private function OnlineGenerationDteXml($company, $frame)
    {
        $xml = "<soapenv:Envelope xmlns:soapenv=\"http://schemas.xmlsoap.org/soap/envelope/\" xmlns:web=\"http://webservices.online.webapp.paperless.cl\">
                    <soapenv:Header />
                    <soapenv:Body>
                    <web:OnlineGenerationDte>
                        <web:param0>" . substr($company->rut, 0, -2) . "</web:param0>
                        <web:param1>" . $company->sovos_user . "</web:param1>
                        <web:param2>" . $company->sovos_password . "</web:param2>
                        <web:param3>" . $frame . "</web:param3>
                        <web:param4>1</web:param4>
                        <web:param5>6</web:param5>
                    </web:OnlineGenerationDte>
                    </soapenv:Body>
                </soapenv:Envelope>";

        return $xml;
    }

    private function OnlineGenerationDte($integration_mode, $xml, $orderId, $ecommerce)
    {
        $sovos_prd = 'http://192.168.18.112:8080/axis2/services/Online?wsdl';
        $sovos_qas = 'http://192.168.18.120:8080/axis2/services/Online?wsdl';

        $url = ($integration_mode === 'PRD') ? $sovos_prd : $sovos_qas;

        try {
            $client = new Client([
                'timeout' => 20,
                'headers' => [
                    'Content-Type' => 'text/xml; charset=UTF-8',
                    'SOAPAction' => 'urn:OnlineGenerationDte',
                ]
            ]);

            $this->changeStatus($orderId, 3, $ecommerce); // Sovos: Enviado
            $response = $client->post($url, ['body' => $xml]);

            $response = $response->getBody()->getContents();
            $code = $this->everything_in_tags("Codigo", $response);
            $message = $this->everything_in_tags("Mensaje", $response);

            if ($code === '0' && strlen($message) >= 5) {

                $order = Order::find($orderId);
                $order->invoice_number = $message;
                $order->save();

                $this->changeStatus($orderId, 5, $ecommerce); // Sovos: Emitido
            } else {
                $this->changeStatus($orderId, 4, $ecommerce); // Sovos: Error
                Log::channel('sovos')->info($xml);
            }
        } catch (\Exception $e) {
            Log::channel('sovos')->error($e->getMessage());
            $this->changeStatus($orderId, 4, $ecommerce); // Sovos: Error
            Log::channel('sovos')->info($xml);
        }
    }

    private function OnlineRecoveryXml($company, $invoice_number)
    {
        $xml = "<soapenv:Envelope xmlns:soapenv=\"http://schemas.xmlsoap.org/soap/envelope/\" xmlns:web=\"http://webservices.online.webapp.paperless.cl\">
                    <soapenv:Header />
                    <soapenv:Body>
                    <web:OnlineGenerationDte>
                        <web:param0>" . substr($company->rut, 0, -2) . "</web:param0>
                        <web:param1>" . $company->sovos_user . "</web:param1>
                        <web:param2>" . $company->sovos_password . "</web:param2>
                        <web:param3>39</web:param3>
                        <web:param4>" . $invoice_number . "</web:param4>
                        <web:param5>2</web:param5>
                    </web:OnlineGenerationDte>
                    </soapenv:Body>
                </soapenv:Envelope>";

        return $xml;
    }

    private function changeStatus($orderId, $statusId, $ecommerce)
    {
        $order = Order::find($orderId);
        $order->order_status_id = $statusId;
        $order->save();

        $status = OrderStatus::where('id', $statusId)->pluck('slug')->first();

        $woocommerce = new WooClient(
            $ecommerce->url,
            $ecommerce->api_key,
            $ecommerce->api_secret,
            [
                'wp_api' => true,
                'version' => 'wc/v3',
                'timeout' => 20
            ]
        );

        $woocommerce->put("orders/{$order->order_number}", [
            'status' => $status
        ]);
    }

    private function everything_in_tags($tagname, $string)
    {
        $string = strtr($string, ['&lt;' => '<']);
        $pattern = "#<\s*?$tagname\b[^>]*>(.*?)</$tagname\b[^>]*>#s";
        preg_match($pattern, $string, $matches);
        return $matches[1];
    }
}
