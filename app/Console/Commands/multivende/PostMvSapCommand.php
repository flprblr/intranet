<?php

namespace App\Console\Commands\multivende;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Facades\Config;
use App\Models\MvOrder;
use App\Models\MvWarehouse;
use App\Models\MvMarketplace;
use App\Models\MvOrderDetail;
use App\Models\MvOrderPayment;

class PostMvSapCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'post:mvsap';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Post Multivende Orders to SAP';

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
                //->where('id', 1)
                ->whereIn('order_status_id', [8, 12, 13]) //Crear status nuevo para los errores de SAP (12)
                ->orderBy('id')
                ->get();

            foreach ($orders as $order) {
                $company = $order->warehouse->company;

                $json = $this->create_json($order, $company);
                if (!empty($json)) {
                    $this->changeStatus($order->id, 13); // SAP: Enviado (13)
                    $result = $this->send_sap($json, $integration_mode);
                    if ($result >= 200 && $result <= 299) {
                        $this->changeStatus($order->id, 14); // SAP: Emitido (14)
                    } else {
                        $this->changeStatus($order->id, 12); // SAP: Error (12)
                    }
                } else {
                    $this->changeStatus($order->id, 12); 
                }
            }
        } catch (\Exception $e) {
            Log::channel('mv-sap')->error($e->getMessage());
        }
        $endTime = Carbon::now();
        $executionTime = $endTime->diffInSeconds($startTime);
        Log::channel('mv-sap')->error('POST SAP Orders: ' . $executionTime . ' seconds');
        return Command::SUCCESS;
    }

    private function create_json($order, $company)
    {
        $warehouse = MvWarehouse::where('id', $order->id_warehouse)->first();
        $marketplace = MvMarketplace::where('id', $order->marketplace_id)->first();
        $orderDetails = MvOrderDetail::where('order_id', $order->id)->get();
        $orderPayments = MvOrderPayment::where('order_id', $order->id)->first();

        try {
            $consignments = [];

            $items = [];
            foreach ($orderDetails as $orderDetail) {
                $item = [
                    'almacen' => $warehouse->lgort,
                    'sku' => $orderDetail->sku,
                    'canti' => "$orderDetail->quantity",
                    'monto' => "$orderDetail->final_price",
                    'descu' => "$orderDetail->discount",
                ];
                $items[] = $item;
            }


            $consignments = [];
            $consignment = [
                'nro_consignment' => $order->order_number,
                'tienda' => $warehouse->werks,
                'deliveryMode' => 'standard_net',
                'orderClass' => $marketplace->auart,
                'invoiceType' => $marketplace->auart,
                'shipmentClass' => '02',
                'item' => $items,
                'pago' => [
                    'medPag' => $orderPayments->sap_id,
                    'monto' => "$order->total",
                    'folio' => $order->invoice_number,
                ],
            ];
            $consignments[] = $consignment;

            $data = [
                'cabe' => [
                    'numPed' => $order->order_number,
                    'nomCli' => $this->quitarAcentos($order->billing_name),
                    'apeCli' => $this->quitarAcentos($order->billing_last_name),
                    'rutCli' => $order->billing_rut,
                    'dirEnv' => $this->quitarAcentos($order->billing_address_1),
                    'comuna' => $this->quitarAcentos($order->billing_city),
                    'ciudad' => $this->quitarAcentos($order->billing_state),
                    'telefono' => $order->billing_phone,
                    'mail' => $this->quitarAcentos($order->billing_email),
                    'fePed' => $this->formatoFecha($order->date_created),
                    'orgVta' => $warehouse->vkorg,
                    'conExp' => 'Z1',
                    'codVend' => null,
                    'vendedor' => null,
                ],
                'deta' => [
                    'consignments' => $consignments,
                ],
                'pago' => [
                    'medPag' => $orderPayments->sap_id,
                    'monto' => "$order->total",
                    'folio' => $order->invoice_number,
                ],
            ];

            // Convierte el array a JSON
            $json = json_encode($data);
        } catch (\Exception $e) {
            // Manejo de errores
            return response()->json(['error' => $e->getMessage()], 500);
        }

        return $json;
    }
    private function send_sap($json, $integration_mode)
    {
        $urls = [
            'PRD' => "https://cloud-foundry-productive-a0hh793q.it-cpi019-rt.cfapps.us10-002.hana.ondemand.com/http/Hy/PedVenta_Create_COM_v12",
            'QAS' => "https://cloud-foundry-m0ef0pmb.it-cpi019-rt.cfapps.us10-002.hana.ondemand.com/http/Hy/PedVenta_Create_COM_v12"
        ];

        $auth = [
            'PRD' => "c2ItNzM2MzM1OGItMmQyYy00NjNlLWI0Y2UtMTRlNDA4MTNlN2MxIWIxNzYxMjJ8aXQtcnQtY2xvdWQtZm91bmRyeS1wcm9kdWN0aXZlLWEwaGg3OTNxIWI1NjE4Njo2ZWNiZDBlMi1mNTVkLTQ5MTEtOGNhYi1hZmNlZTY1YzBiYmUkN2FaZFI4VXRRTmZwS0E3VWttOXdFeWRuVEhiMEJCVklJX1RVT0M4MXktTT0=",
            'QAS' => "c2ItYmYzZWY5ZDYtOWNiZS00NTA2LWIyODItZTczNzU0MTdhNGM3IWIxNTcxMTR8aXQtcnQtY2xvdWQtZm91bmRyeS1tMGVmMHBtYiFiNTYxODY6NjZhYTY4MWUtOTI4NC00NzMyLThmM2UtYzI0NWU0OTAwYTQ3JFNVaG9RbUtkM25JamRuN2NsR3c3SVNKOVczaFpTdGtZS0NGN040VE9wUjA9"
        ];

        $client = new Client();

        try {

            $response = $client->request('POST', $urls[$integration_mode], [
                'headers' => [
                    'Authorization' => 'Basic ' . $auth[$integration_mode],
                    'Content-Type' => 'application/json',
                ],
                'body' => $json,
            ]);
        } catch (RequestException $e) {
            if ($e->hasResponse()) {
                $response = $e->getResponse();
                $statusCode = $response->getStatusCode();
                $body = $response->getBody()->getContents();
                // Manejar la respuesta de error
                //echo "Error en la solicitud: Código: $statusCode, Mensaje: $body";
                Log::channel('mv-sap')->error('Error en la solicitud, Código: ' . $statusCode . ' , Mensaje: ' . $body);
            } else {
                // Manejar otros errores de la solicitud
                //echo "Error en la solicitud: " . $e->getMessage();
                Log::channel('mv-sap')->error("Error en la solicitud: " . $e->getMessage());
            }
        }
        return $response->getStatusCode();
    }

    private function changeStatus($orderId, $statusId)
    {
        $order = MvOrder::find($orderId);
        $order->order_status_id = $statusId;
        $order->save();
    }

    private function formatoFecha($fecha)
    {
        // Desglosar fecha
        $fechaDesglosada = explode('-', $fecha);

        // Obtener elementos de la fecha
        $anio = $fechaDesglosada[0];
        $mes = $fechaDesglosada[1];
        $dia = $fechaDesglosada[2];

        // Formatear
        return "$dia$mes$anio";
    }

    private function quitarAcentos($cadena)
    {
        $buscar = ['á', 'é', 'í', 'ó', 'ú', 'ñ', 'Á', 'É', 'Í', 'Ó', 'Ú', 'Ñ'];
        $reemplazar = ['a', 'e', 'i', 'o', 'u', 'n', 'A', 'E', 'I', 'O', 'U', 'N'];
        return strtr($cadena, array_combine($buscar, $reemplazar));
    }
}
