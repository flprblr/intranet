<?php

namespace App\Console\Commands\multivende;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

use Carbon\Carbon;
use GuzzleHttp\Client;

use App\Models\MvOrder;
use App\Models\MvWarehouse;
use App\Models\Company;

class GetMvSovosCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'get:mvsovos';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get Multivende Orders to Sovos';

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
            $orders = MvOrder::with(['warehouse.company'])
                ->whereNull('invoice_url')
                ->where('order_status_id', 5)
                ->orderBy('id')
                ->get();

            foreach ($orders as $order) {
                $warehouse = $order->warehouse;
                $company = $warehouse->company;

                $xml = $this->OnlineRecoveryXml($company, $order->invoice_number);
                $this->OnlineRecovery($integration_mode, $xml, $order->id);
            }
        } catch (\Exception $e) {
            Log::channel('mv-sovos')->error($e->getMessage());
        }

        $endTime = Carbon::now();
        $executionTime = $endTime->diffInSeconds($startTime);
        Log::channel('mv-sovos')->error('GET Sovos Pdf: ' . $executionTime . ' seconds');

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
                        <web:param3>39</web:param3>
                        <web:param4>" . $invoiceNumber . "</web:param4>
                        <web:param5>2</web:param5>
                    </web:OnlineRecovery>
                    </soapenv:Body>
                </soapenv:Envelope>";

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

            $order = MvOrder::find($orderId);
            $order->invoice_url = $message;
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
