<?php

namespace App\Console\Commands\multivende;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Artisan;
use App\Models\MvAccess;
use Illuminate\Support\Facades\Cache;

class GetMvWarehouseCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'get:mvwarehouses';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get Multivende Warehouses';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $startTime = Carbon::now();

        try {
            // Llamar al comando get:mvaccess desde dentro de este comando
            $exitCode = Artisan::call('get:mvaccess');
            if ($exitCode === 0) {
                $access = MvAccess::query()->first();
                $warehouse = $this->getWarehouse($access);

                Cache::put('warehouse_data', $warehouse, now()->addHours(1)); // Almacena los datos en la caché durante una hora

                //$this->info('Datos del almacén almacenados en la caché.');
            } else {
                Log::channel('mv-multivende')->error('Error de Conexión a Multivende');
            }
        } catch (\Exception $e) {
            //Log::error($e->getMessage());
            Log::channel('mv-multivende')->error('Error de Conexión');
        }

        $endTime = Carbon::now();
        $executionTime = $endTime->diffInSeconds($startTime);
        Log::channel('mv-multivende')->error('Get MV->Warehouse: ' . $executionTime . ' seconds');

        return Command::SUCCESS;
    }

    private function getWarehouse($access)
    {
        $warehouse_data = [];

        $client = new Client();

        $response = $client->get($access->base_url . "api/m/" . $access->merchant_id . "/stores-and-warehouses/p/1", [
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $access->token,
            ],
        ]);

        $body = $response->getBody();
        $warehouse = json_decode($body);

        foreach ($warehouse->entries as $entry) {
            $warehouse_data[] = [
                'name' => $entry->name,
                'id' => $entry->_id,
            ];
        }

        return $warehouse_data;
    }
}
