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
use Illuminate\Support\Facades\Config;
use GuzzleHttp\Client;

class GetSovosReverseCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'get:sovosReverse';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get E-Commerce Orders to Sovos';

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
            $ecommerces = Ecommerce::select('id', 'company_id')->get();

            foreach ($ecommerces as $ecommerce) {

                $company = Company::select('id', 'rut', 'sovos_user', 'sovos_password')
                    ->where('id', $ecommerce->company_id)
                    ->first();

                $orders = Order::select('id', 'invoice_number_rev')
                    ->where('order_status_id', 12)
                    ->where('ecommerce_id', $ecommerce->id)
                    ->whereNotNull('invoice_number')
                    ->get();

                foreach ($orders as $order) {
                    $xml = $this->OnlineRecoveryXml($company, $order->invoice_number_rev);
                    $this->OnlineRecovery($integration_mode, $xml, $order->id);
                }
            }
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }

        $endTime = Carbon::now();
        $executionTime = $endTime->diffInSeconds($startTime);
        Log::info('Get Orders->Sovos: ' . $executionTime . ' seconds');

        return Command::SUCCESS;
    }

    private function OnlineRecoveryXml($company, $invoiceNumber)
    {
        $xml = "<soapenv:Envelope xmlns:soapenv=\"http://schemas.xmlsoap.org/soap/envelope/\" xmlns:web=\"http://webservices.online.webapp.paperless.cl\">
                    <soapenv:Header />
                    <soapenv:Body>
                    <web:OnlineRecovery>
                        <web:param0>" . substr($company->rut, 0, -2) . "</web:param0>
                        <web:param1>" . $company->sovos_user . "</web:param1>
                        <web:param2>" . $company->sovos_password . "</web:param2>
                        <web:param3>61</web:param3>
                        <web:param4>" . $invoiceNumber . "</web:param4>
                        <web:param5>2</web:param5>
                    </web:OnlineRecovery>
                    </soapenv:Body>
                </soapenv:Envelope>";
        Log::info($xml);
        return $xml;
    }

    private function OnlineRecovery($integration_mode, $xml, $orderId)
    {
        // WHM
        // $sovos_prd = 'http://190.54.179.88:8080/axis2/services/Online?wsdl';
        // $sovos_qas = 'http://190.54.179.110:8080/axis2/services/Online?wsdl';

        // LOCAL
        $sovos_prd = 'http://192.168.18.112:8080/axis2/services/Online?wsdl';
        $sovos_qas = 'http://192.168.18.120:8080/axis2/services/Online?wsdl';

        $url = ($integration_mode === 'PRD') ? $sovos_prd : $sovos_qas;

        $client = new Client([
            'timeout' => 10,
            'headers' => [
                'Content-Type' => 'text/xml; charset=UTF-8',
                'SOAPAction' => 'urn:OnlineRecovery',
            ]
        ]);

        $response = $client->post($url, ['body' => $xml]);

        $response = $response->getBody()->getContents();
        $code = $this->everything_in_tags("Codigo", $response);
        $message = $this->everything_in_tags("Mensaje", $response);

        if ($code === '0' && strpos($message, 'http') === 0) {

            $order = Order::find($orderId);
            $order->invoice_url_rev = $message;
            $order->save();
        }
    }

    private function everything_in_tags($tagname, $string)
    {
        $string = strtr($string, ['&lt;' => '<']);
        $pattern = "#<\s*?$tagname\b[^>]*>(.*?)</$tagname\b[^>]*>#s";
        preg_match($pattern, $string, $matches);
        return $matches[1];
    }
}
