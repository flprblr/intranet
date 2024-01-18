<?php

namespace App\Console\Commands\multivende;

use Illuminate\Console\Command;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Facades\Config;
use App\Models\MvOrder;
use App\Models\MvAccess;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Artisan;

class PostMvDocument extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'post:mvdocument';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Post Document to Multivende';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $startTime = Carbon::now();

        try {

            $exitCode = Artisan::call('get:mvaccess');
            if ($exitCode === 0) {
                $orders = MvOrder::with('warehouse.company')
                    ->where('payment_status', 'completed')
                    //->where('id', 129)
                    ->whereIn('order_status_id', [14, 15, 16]) //Crear status nuevo para los errores de SAP (12)
                    ->orderBy('id')
                    ->get();

                foreach ($orders as $order) {
                    $this->changeStatus($order->id, 16); // Envio documento (16)
                    $result = $this->processOrder($order);

                    if ($result === false) {
                        $this->changeStatus($order->id, 15); // Error Envio documento (15)
                    }
                }
            } else {
                Log::channel('mv-multivende')->error('Error de Conexión a Multivende');
            }
        } catch (\Exception $e) {
            Log::channel('mv-multivende')->error($e->getMessage());
        }

        $endTime = Carbon::now();
        $executionTime = $endTime->diffInSeconds($startTime);
        Log::channel('mv-multivende')->error('POST SAP Orders: ' . $executionTime . ' seconds');
        return Command::SUCCESS;
    }

    private function processOrder($order)
    {
        $access = MvAccess::query()->first();
        $electronic_billing_document_id = $this->CreateCheckoutElectronicBillingDocument($order, $access);

        if ($electronic_billing_document_id === false) {
            Log::channel('mv-multivende')->error('Error al crear electronic_billing_document_id');
            return false;
        }

        $directory = $this->DownloadPdf($order);
        if ($directory === false) {
            Log::channel('mv-multivende')->error('Error al Descargar Pdf de Orden: ' . $order->order_number);
            return false;
        }

        $response = $this->UploadPdf($directory, $order, $access, $electronic_billing_document_id);
        if ($response !== 200) {
            Log::channel('mv-multivende')->error('Error al cargar documento de Orden: ' . $order->order_number . ' en Multivende');
            return false;
        }

        Storage::disk('multivende')->delete($directory);
        $this->changeStatus($order->id, 17); // Emitido
        return true;
    }
    private function createCheckoutElectronicBillingDocument($order, $access)
    {
        try {
            $httpClient = new Client();
            $EBDocumentEmitterId = "e3437515-076f-454a-bc91-2bf42e57e1f0";

            $url = $access->base_url . 'api/checkouts/' . $order->checkout_id . '/electronic-billing-documents';
            $response = $httpClient->post($url, [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $access->token,
                ],
                'json' => [
                    'ClientId' => $order->client_id,
                    'parameters' => 'Boleta Belsport',
                    'code' => $order->invoice_number,
                    'name' => $order->invoice_number,
                    'id' => $order->invoice_number,
                    'content' => '{a:1}', // Aquí deberías especificar el contenido correcto en formato JSON
                    'emissionDate' => $order->date_created,
                    'type' => 'electronic_billing_electronic_bill',
                    'provider' => 'belsport_group',
                    'ElectronicBillingDocumentEmitterId' => $EBDocumentEmitterId,
                ],
            ]);

            $statusCode = $response->getStatusCode();
            $responseBody = $response->getBody()->getContents();
            $data = json_decode($responseBody, true);

            if ($statusCode == 201 || $statusCode == 200) {
                return $data['_id'];
            } else {
                return false;
            }
        } catch (\Exception $e) {
            Log::channel('mv-multivende')->error('Error CreateCheckoutElectronicBillingDocument: ' . $e->getMessage());
            return false;
        }
    }

    private function DownloadPdf($order)
    {
        // Crear una instancia del cliente Guzzle
        $client = new Client();

        try {
            // Realizar la solicitud GET para descargar el archivo PDF
            $response = $client->get($order->invoice_url);

            if ($response->getStatusCode() === 200) {
                // Obtener el contenido de la respuesta
                $content = $response->getBody()->getContents();

                $filename = $order->invoice_number . '.pdf';

                if (empty($content) || empty($filename)) {
                    Log::channel('mv-multivende')->error('El contenido o el nombre del archivo está vacío. No se envió nada al FTP.');
                }
                try {
                    //$result = Storage::disk('sftp')->put($directory . $filename, $content);
                    $result = Storage::disk('multivende')->put($filename, $content);

                    if ($result) {
                        // Log::channel('adidas')->info('Archivo ' . $filename . ' almacenado con éxito en el FTP en el directorio ' . $directory);
                    } else {
                        Log::channel('mv-multivende')->error('Hubo un problema al guardar el archivo ' . $filename);
                    }
                } catch (\Exception $e) {
                    Log::channel('mv-multivende')->error('Error al intentar guardar documento: ' . $e->getMessage());
                }

                return $filename;
            } else {
                Log::channel('mv-multivende')->error('Error al descargar el archivo PDF desde la URL ' . $order->invoice_url);
                return false;
            }
        } catch (\Exception $e) {
            Log::channel('mv-multivende')->error('Error al descargar el archivo PDF: ' . $e->getMessage());
            return false;
        }
    }

    private function uploadPdf($directory, $order, $access, $electronic_billing_document_id)
    {
        $fileContent = Storage::disk('multivende')->get($directory);
        $url = 'http://app.multivende.com/api/electronic-billing-documents/' . $electronic_billing_document_id . '/upload';

        try {
            $client = new Client();

            $response = $client->request('POST', $url, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $access->token,
                ],
                'multipart' => [
                    [
                        'name' => 'file',
                        'contents' => $fileContent,
                        'filename' => $order->invoice_number . '.pdf',
                    ],
                ],
            ]);

            return $response->getStatusCode();
        } catch (GuzzleException $e) {
            // Manejo de errores
            return $e->getMessage();
        }
    }

    private function changeStatus($orderId, $statusId)
    {
        $order = MvOrder::find($orderId);
        $order->order_status_id = $statusId;
        $order->save();
    }
}
