<?php

namespace App\Console\Commands\multivende;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use GuzzleHttp\Client;
use App\Models\MvAccess;
use App\Models\MvStore;
use App\Models\MvOrder;
use App\Models\MvMarketplace;
use App\Models\MvWarehouse;
use App\Models\MvOrderDetail;
use App\Models\MvOrderPayment;
use Illuminate\Support\Facades\Artisan;

class GetMvOrdersManualCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'get:mvordersmanual {id} {order_number} {startDate} {endDate}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $startTime = Carbon::now();
        $idStore = $this->argument('id');
        $orderNumber = $this->argument('order_number');
        $startDate = $this->argument('startDate');
        $endDate = $this->argument('endDate');

        try {
            // Llamar al comando get:mvaccess desde dentro de este comando
            $exitCode = Artisan::call('get:mvaccess');
            if ($exitCode === 0) {
                $stores = MvStore::where('id', $idStore)
                    ->orderBy('marketplace_id', 'asc')
                    ->get();

                $access = MvAccess::query()->first();

                foreach ($stores as $store) {
                    $marketplace = MvMarketplace::where('id', $store->marketplace_id)->first();

                    $account = $this->getConnection($access->base_url, $access->merchant_id, $access->token, $store->connection, $marketplace->connection);
                    $this->getCheckOutsLight($access->base_url, $access->merchant_id, $access->token, $account, $startDate, $endDate, $store->marketplace_id, $store->id, $store->connection, $orderNumber);
                }
            } else {
                Log::channel('mv-multivende')->error('Error de Conexión a Multivende');
            }
        } catch (\Exception $e) {
            //Log::error($e->getMessage());
            Log::channel('mv-multivende')->error('Error de Conexión');
        }

        $endTime = Carbon::now();
        $executionTime = $endTime->diffInSeconds($startTime);
        Log::channel('mv-multivende')->error('Get MV->Orders: ' . $executionTime . ' seconds');

        return Command::SUCCESS;
    }

    private function getConnection($base_url, $merchant_id, $token, $store_connection, $mk_connection)
    {
        $url = "{$base_url}api/m/{$merchant_id}/{$mk_connection}/p/%7B%7Bpage%7D%7D";

        $response = (new Client())->get($url, [
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $token,
            ],
        ]);

        $data = json_decode($response->getBody(), true);

        foreach ($data['entries'] as $entry) {
            if ($store_connection === $entry['_name']) {
                return $entry['_id'];
            }
        }

        return "";
    }

    private function getCheckOutsLight($base_url, $merchant_id, $token, $account, $startDate, $endDate, $marketplace_id, $store_id, $connection, $orderNumber)
    {
        if (!empty($account)) {
            try {
                $httpClient = new Client();
                $url = $base_url . 'api/m/' . $merchant_id . '/checkouts/light/p/%7B%7Bpage%7D%7D?_marketplace_connection_id=' . $account . '&_sold_at_from=' . $startDate . '&_sold_at_to=' . $endDate . '';
                $response = $httpClient->get($url, [
                    'headers' => [
                        'Accept' => 'application/json',
                        'Content-Type' => 'application/json',
                        'Authorization' => 'Bearer ' . $token,
                    ],
                ]);

                $data = json_decode($response->getBody(), true);
                //Log::info(json_encode($data));
                $totalPages = $data['pagination']['total_pages'];

                for ($i = 1; $i <= $totalPages; $i++) {
                    $url = $base_url . 'api/m/' . $merchant_id . '/checkouts/light/p/' . $i . '?_marketplace_connection_id=' . $account . '&_sold_at_from=' . $startDate . '&_sold_at_to=' . $endDate . '';
                    $response = $httpClient->get($url, [
                        'headers' => [
                            'Accept' => 'application/json',
                            'Content-Type' => 'application/json',
                            'Authorization' => 'Bearer ' . $token,
                        ]
                    ]);

                    $orders = json_decode($response->getBody(), true);
                    //Log::info(json_encode($orders));

                    foreach ($orders['entries'] as $entry) {
                        $checkout_id = $entry['_id'];
                        $result = MvOrder::where('checkout_id', $checkout_id)->get();
                        if ($result->isEmpty()) {
                            $this->getCheckOut($checkout_id, $marketplace_id, $base_url, $token, $store_id, $connection, $orderNumber);
                        }
                    }
                }
            } catch (\Exception $e) {
                Log::channel('mv-multivende')->error($e->getMessage());
            }
        }
    }

    private function getCheckOut($checkout_id, $marketplace_id, $base_url, $token, $store_id, $connection, $orderNumber)
    {

        $httpClient = new Client();

        try {
            sleep(1); // Duerme el script durante 1 segundo

            $response = $httpClient->get($base_url . 'api/checkouts/' . $checkout_id . '', [
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $token,
                ]
            ]);

            // Obtiene el cuerpo de la respuesta como una cadena JSON
            $body = $response->getBody()->getContents();

            // Decodifica la cadena JSON en un array asociativo
            $bodyArray = json_decode($body, true);

            // Obtiene el valor de "externalCustomerOrderNumber" del objeto CheckoutLink
            $externalCustomerOrderNumber = $bodyArray['CheckoutLink']['externalCustomerOrderNumber'];
            //Log::info("N° Orden: " . $externalCustomerOrderNumber);

            //$order = MvOrder::where('order_number', $externalCustomerOrderNumber)->first();

            //if (is_null($order)) {
            if ($externalCustomerOrderNumber === $orderNumber || $orderNumber === null) {
                // Obtiene el objeto "Client" del JSON de respuesta
                $clientObject = $bodyArray['Client'];
                //Log::info($clientObject);
                $client_id = $clientObject['_id'];
                $phone = $clientObject['phoneNumber'];
                $taxid = strtoupper($clientObject['taxId']);
                $name = mb_convert_case($clientObject['name'], MB_CASE_TITLE, 'UTF-8');
                $lastname = mb_convert_case($clientObject['lastName'], MB_CASE_TITLE, 'UTF-8');

                if ($marketplace_id == 1) {
                    $email = 'mercadolibre@multivende.cl';
                } elseif ($marketplace_id == 4) {
                    $email = 'fcom@multivende.cl';
                } else {
                    $email = $clientObject['email'];
                }

                $phone = in_array($phone, ['XXXXXXX', null]) ? '999999999' : $phone;
                $taxid = in_array($taxid, ['000000000', null, 'E10761592']) ? '666666666' : $taxid;
                $name = $name ?? 'Multivende';
                $lastname = $lastname ?? 'Multivende';
                $email = $email ?? 'sincorreo@multivende.cl';

                // Obtiene el array "DeliveryOrderInCheckouts" del JSON de respuesta
                $deliveryOrders = $bodyArray['DeliveryOrderInCheckouts'];
                //Log::info(json_encode($deliveryOrders));

                // Nuevo array para almacenar los items de "DeliveryOrderInCheckoutItems"
                $deliveryOrderItems = [];

                // Itera a través de los objetos "DeliveryOrderInCheckouts"
                foreach ($deliveryOrders as $deliveryOrder) {
                    if (isset($deliveryOrder['DeliveryOrderInCheckoutItems'])) {

                        // Agrega los items al nuevo array
                        foreach ($deliveryOrder['DeliveryOrderInCheckoutItems'] as $item) {
                            $deliveryOrderItems[] = $item;
                        }
                    }
                }

                // Obtiene el objeto "DeliveryOrder" del primer elemento en el array
                $deliveryOrderObject = $deliveryOrders[0]['DeliveryOrder'];
                //Log::info($deliveryOrderObject);

                // Verifica si "ShippingAddress" no es null
                if (!is_null($deliveryOrderObject['ShippingAddress'])) {
                    $shippingAddress = $deliveryOrderObject['ShippingAddress'];

                    // Accede a las propiedades del objeto "ShippingAddress"
                    $city = $shippingAddress['city'];
                    $region = !is_null($shippingAddress['state']) ? $shippingAddress['state'] : $shippingAddress['country'];
                    $address = $shippingAddress['street'] . ' ' . $shippingAddress['number'];

                    // Verifica si las propiedades específicas son null y ajusta la dirección en consecuencia
                    if (is_null($shippingAddress['street']) && is_null($shippingAddress['number'])) {
                        $address = $shippingAddress['address_1'];
                    }
                } else {
                    Log::channel('mv-multivende')->error("N° Orden: " . $externalCustomerOrderNumber . "No tiene Direccion de Envio");
                }

                // CLIENTE
                $listClient = [];

                $client = [
                    'taxid' => $taxid,
                    'name' => $name,
                    'lastname' => $lastname,
                    'email' => $email,
                    'phone' => $phone,
                    'address' => $address,
                    'city' => $city,
                    'region' => $region
                ];

                $listClient[] = $client;
                //Log::info(json_encode((array)$client));

                // Array CheckoutItems (PRODUCTOS)
                $CheckoutItemsArray = $bodyArray['CheckoutItems'];
                //Log::info(json_encode($CheckoutItemsArray));

                $listPrd = [];

                foreach ($CheckoutItemsArray as $checkoutItem) {
                    $productoCode = $checkoutItem['code'];

                    // BUSCAR CODIGO SI NO EXISTE EN CODE
                    if ($productoCode === null) {
                        $productVersionObj = $checkoutItem['ProductVersion'];
                        $productoCode = $productVersionObj['code'];
                    }

                    foreach ($deliveryOrderItems as $deliveryOrderItem) {
                        $fulfilledWarehouse = $deliveryOrderItem['FulfilledWarehouse'];
                        $productVersion = $deliveryOrderItem['ProductVersion'];

                        if ($productoCode === $productVersion['code']) {
                            $id_warehouse = $fulfilledWarehouse['_id'];
                        }
                    }

                    $discount = $checkoutItem['discount'];

                    // CALCULA TOTAL DE BOLETA
                    $gross = $checkoutItem['gross']; // PRECIO UNITARIO SIN DESCUENTO
                    $count = $checkoutItem['count']; // CANTIDAD
                    $totalLinea = ($gross * $count) - $discount;

                    $product = [
                        'code' => $productoCode,
                        'name' => '',
                        'count' => $count,
                        'gross' => $gross,
                        'discount' => $discount,
                        'id_warehouse' => $id_warehouse,
                        'total_linea' => $totalLinea
                    ];

                    $listPrd[] = $product;
                }
                //Log::info($listaPrd);

                //COSTO DE ENVIO
                $costoEnvio = 0;
                foreach ($deliveryOrders as $deliveryOrder) {
                    if (isset($deliveryOrder['DeliveryOrder'])) {
                        $deliveryOrderObj1  = $deliveryOrder['DeliveryOrder'];

                        if (isset($deliveryOrderObj1['cost'])) {
                            $cost = $deliveryOrderObj1['cost'];
                            $costoEnvio += $cost;
                        }
                    }
                }
                $shippingCost = $costoEnvio;

                if ($shippingCost !== 0) {
                    foreach ($deliveryOrderItems as $deliveryOrderItem) {
                        if (isset($deliveryOrderItem['FulfilledWarehouse'])) {
                            $fulfilledWarehouse = $deliveryOrderItem['FulfilledWarehouse'];
                            if (!empty($fulfilledWarehouse) && isset($fulfilledWarehouse['_id'])) {
                                $id_warehouse = $fulfilledWarehouse['_id'];
                                break;
                            }
                        }
                    }
                    $listPrd[] = [
                        'code' => '000000001000000031',
                        'name' => 'GASTOS DE ENVIO',
                        'count' => '1',
                        'gross' => $shippingCost,
                        'discount' => '0',
                        'id_warehouse' => $id_warehouse,
                        'total_linea' =>  $shippingCost
                    ];
                }

                //Log::info($listPrd);

                // Convertir el array en una colección
                $listaPrdColeccion = collect($listPrd);

                // Usar sortBy() en la colección
                $listaPrdOrdenada = $listaPrdColeccion->sortBy('id_warehouse');

                $prd = $listaPrdOrdenada->groupBy('id_warehouse');
                //Log::info($prd);

                foreach ($prd as $id_warehouse => $products) {

                    // Array CheckoutPayments (PAGOS)
                    $CheckoutPaymentsArray = $bodyArray['CheckoutPayments'];
                    //Log::info(json_encode((array)$CheckoutPaymentsArray));

                    foreach ($CheckoutPaymentsArray as $payment) {
                        $payStatus = $payment['paymentStatus'];
                        $payFecha = $payment['updatedAt'];
                    }

                    $total_amount = 0;

                    foreach ($products as $prd) {
                        $total_amount += $prd['total_linea'];
                    }

                    $result = MvMarketplace::select('*')
                        ->join('mv_payments', 'mv_marketplaces.payment_id', '=', 'mv_payments.id')
                        ->where('mv_marketplaces.id', $marketplace_id)
                        ->first();

                    $fechaCarbon = Carbon::parse($payFecha);
                    $date = $fechaCarbon->format('Y-m-d');

                    foreach ($products as $prd) {
                        $warehouse = MvWarehouse::where('id_warehouse', $prd['id_warehouse'])->first();
                    }

                    $order = new MvOrder();
                    $order->prefix = $result->marketplace;
                    $order->order_number = $externalCustomerOrderNumber;
                    $order->date_created = $date;
                    $order->net = intval($total_amount) / 1.19;
                    $order->tax = intval($total_amount) - (intval($total_amount) / 1.19);
                    $order->total = intval($total_amount);
                    $order->billing_name = ucwords(strtolower($listClient[0]['name']));
                    $order->billing_last_name = ucwords(strtolower($listClient[0]['lastname']));
                    $order->billing_rut = $this->formatRut($listClient[0]['taxid']);
                    $order->billing_phone = str_replace(' ', '', $listClient[0]['phone']);
                    $order->billing_email = strtolower($listClient[0]['email']);
                    $order->billing_state = str_replace(["'", "\\"], "", $listClient[0]['region']);
                    $order->billing_city = str_replace(["'", "\\"], "", $listClient[0]['city']);
                    $order->billing_address_1 = str_replace(["'", "\\"], "", $listClient[0]['address']);
                    $order->payment_status = $payStatus;
                    $order->order_status_id = 1;
                    $order->marketplace_id = $marketplace_id;
                    $order->store_id = $store_id;
                    $order->id_warehouse = $warehouse->id;
                    $order->checkout_id = $checkout_id;
                    $order->client_id = $client_id;
                    $order->save();

                    foreach ($products as $product) {

                        $warehouse = MvWarehouse::where('id_warehouse', $product['id_warehouse'])->first();

                        $orderDetail = new MvOrderDetail;
                        $orderDetail->sku = $product['code'];;
                        $orderDetail->description = $product['name'];
                        $orderDetail->unit_price = $product['gross'];
                        $orderDetail->quantity = $product['count'];
                        $orderDetail->subtotal = $product['gross'] * $product['count'];;
                        $orderDetail->final_price = $product['total_linea'];
                        $orderDetail->discount = $product['discount'];
                        $orderDetail->total = $product['total_linea'];
                        $orderDetail->order_id = $order->id;
                        $orderDetail->id_warehouse = $warehouse->id;
                        $orderDetail->save();
                    }

                    $orderPayment = new MvOrderPayment();
                    $orderPayment->description = $result->description;
                    $orderPayment->sap_id = $result->sap_id;
                    $orderPayment->pos_id = $result->pos_id;
                    $orderPayment->amount = $total_amount;
                    $orderPayment->date = $date;
                    $orderPayment->order_id = $order->id;
                    $orderPayment->save();

                    /*Log::info('Get Order: ' . $externalCustomerOrderNumber  . PHP_EOL .
                        'Marketplace: ' . $result->marketplace . PHP_EOL .
                        'Store: '       . $connection . PHP_EOL .
                        'Bodega: '      . $prd['id_warehouse']);*/
                }
            }
        } catch (\Exception $e) {
            Log::channel('mv-multivende')->error($e->getMessage());
        }
    }

    private function formatRut($rut)
    {

        $rut = preg_replace('/[^0-9kK]/', '', $rut);
        $body = substr($rut, 0, -1);
        $dv = substr($rut, -1);

        return $body . '-' . $dv;
    }
}
