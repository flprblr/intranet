<?php

namespace App\Console\Commands\multivende;

use Illuminate\Console\Command;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

use Carbon\Carbon;

use App\Models\MvOrder;
use App\Models\MvOrderDetail;
use App\Models\MvOrderPayment;

use GuzzleHttp\Client;

class PostMvSovosCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'post:mvsovos';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Post Multivende Orders to Sovos';

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

            $orders = MvOrder::with('warehouse.company')
                ->where('payment_status', 'completed')
                //->where('order_number', '2000007002685356')
                ->whereIn('order_status_id', [1, 2, 4])
                ->whereNull('invoice_number')
                ->orderBy('id')
                ->get();

            foreach ($orders as $order) {
                $warehouse = $order->warehouse;
                $company = $warehouse->company;

                if ($this->checkOrder($order, $company)) {
                    $frame = $this->OnlineGenerationDteFrame($company, $order, $warehouse);
                    $xml = $this->OnlineGenerationDteXml($company, $frame);
                    $this->OnlineGenerationDte($integration_mode, $xml, $order, $company);
                }
            }
        } catch (\Exception $e) {
            Log::channel('mv-sovos')->error($e->getMessage());
        }

        $endTime = Carbon::now();
        $executionTime = $endTime->diffInSeconds($startTime);
        Log::channel('mv-sovos')->error('POST MV Sovos Orders: ' . $executionTime . ' seconds');

        return Command::SUCCESS;
    }

    private function checkOrder($order, $company)
    {
        $orderDetails = MvOrderDetail::select('sku')->where('order_id', $order->id)->get();

        $allProductsExist = true;

        foreach ($orderDetails as $orderLine) {
            $result = DB::connection($company->server)->select("SELECT COUNT(*) FROM pos.pos_m_producto WHERE sku = '$orderLine->sku' or upc = '$orderLine->sku'");
            $count = isset($result[0]->count) ? $result[0]->count : 0;

            if ($count === 0) {
                $this->changeStatus($order->id, 2); // POS: Error Producto
                Log::channel('mv-sovos')->error('Producto: ' . $orderLine->sku . ' No existe en POS de ' . $company->server . ' Orden: ' . $order->order_number);
                $allProductsExist = false; 
                break; 
            } else {
                $result = DB::connection($company->server)->select("SELECT name FROM pos.pos_m_producto WHERE sku = '$orderLine->sku' or upc = '$orderLine->sku'");
                $productName = isset($result[0]->name) ? $result[0]->name : '';
                MvOrderDetail::where('order_id', $order->id)
                    ->where('sku', $orderLine->sku)
                    ->update(['description' => $productName]);
            }
        }
        return $allProductsExist;
    }

    private function OnlineGenerationDteFrame($company, $order, $warehouse)
    {
        $separator = "\n";
        $frame = "FH|39||$order->date_created||||||$company->rut|$warehouse->logo|$company->activity|$company->acteco|||$company->address|$company->commune|$company->city||$order->billing_rut|$order->billing_name $order->billing_last_name||" . str_replace('&', '', substr($order->billing_address_1, 0, 59))  . "|" . str_replace('&', '', substr($order->billing_city, 0, 19))  . "|" . str_replace('&', '', substr($order->billing_state, 0, 19))  . "|" . str_replace('&', '', substr($order->billing_address_1, 0, 59))  . "|" . str_replace('&', '', substr($order->billing_city, 0, 19))  . "|" . str_replace('&', '', substr($order->billing_state, 0, 19))  . "|$order->net|||$order->tax|$order->total||$order->total";
        $frame .= $separator;

        $orderDetails = MvOrderDetail::select('id', 'sku', 'description', 'unit_price', 'quantity', 'subtotal', 'final_price', 'discount', 'total', 'order_id')
            ->where('order_id', $order->id)->get();

        $orderPayment = MvOrderPayment::where('order_id', $order->id)->first();

        foreach ($orderDetails as $index => $orderLine) {
            $row = intval($index) + 1;
            $frame .= "DH|$row|0|" . str_replace('&', '', substr($orderLine->description, 0, 36))  . "||$orderLine->quantity|$orderLine->unit_price|$orderLine->total|";
            $frame .= $separator;
            $frame .= "DHCD|$orderLine->sku|$orderLine->sku|";
            $frame .= $separator;
        }

        //$frame .= "PE|$order->order_number|$order->billing_email||$orderPayment->description|$order->billing_phone|$warehouse->des_sovos";
        $frame .= "PE|$order->order_number|multivende@multivende.cl||$orderPayment->description|$order->billing_phone|$warehouse->des_sovos";

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

    private function OnlineGenerationDte($integration_mode, $xml, $order, $company)
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

            $this->changeStatus($order->id, 3); // Sovos: Enviado
            $response = $client->post($url, ['body' => $xml]);

            $response = $response->getBody()->getContents();
            $code = $this->everything_in_tags("Codigo", $response);
            $message = $this->everything_in_tags("Mensaje", $response);

            if ($code === '0' && strlen($message) >= 5) {

                $order = MvOrder::find($order->id);
                $order->invoice_number = $message;
                $order->save();

                $this->changeStatus($order->id, 5); // Sovos: Emitido
            } else {
                $this->changeStatus($order->id, 4); // Sovos: Error
                //Log::info($xml);
                Log::channel('mv-sovos')->error('Error: ' . $message . ' N° Orden: ' . $order->order_number . ' Empresa: ' . $company->name);
            }
        } catch (\Exception $e) {
            Log::channel('mv-sovos')->error($e->getMessage());
        }
    }

    private function changeStatus($orderId, $statusId)
    {
        $order = MvOrder::find($orderId);
        $order->order_status_id = $statusId;
        $order->save();
    }

    private function everything_in_tags($tagname, $string)
    {
        $string = strtr($string, ['&lt;' => '<']);
        $pattern = "#<\s*?$tagname\b[^>]*>(.*?)</$tagname\b[^>]*>#s";
        preg_match($pattern, $string, $matches);
        return $matches[1];
    }
}
