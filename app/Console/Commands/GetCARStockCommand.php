<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

use App\Models\Ecommerce;
use App\Models\Stock;

class GetCARStockCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'get:stock';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get SAP CAR Stock';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $startTime = Carbon::now();

        try {
            
            $countTotal = 0;

            $dsn = env('CAR_DSN');
            $user = env('CAR_USER');
            $password = env('CAR_PASSWORD');

            $conn = odbc_connect($dsn, $user, $password);

            if (!$conn) {
                Log::channel('woo-stocks')->error('SAP S4 connection error.');
            }

            $ecommerces = Ecommerce::select('id','WERKS')->where('status', '1')->get();

            foreach ($ecommerces as $ecommerce) {

                $stocks = Stock::select('id','SKU','woo_stock')
                ->where('ecommerce_id', $ecommerce->id)
                ->get();

                foreach($stocks as $stock) {
                    
                    $countTotal++;

                    $query = "SELECT
                                \"Location\" as werks,
                                \"Article\" as matnr,
                                SUM(\"CurrentStock\") AS zlabst
                            FROM (
                            SELECT
                                \"Article\",
                                SUM(\"CurrentStock\") AS \"CurrentStock\",
                                \"Location\"
                            FROM \"_SYS_BIC\".\"sap.is.retail.car_s4h/InventoryVisibilityWithSalesOrderReservedQuantity\"
                            WHERE \"Location\" = '" . $ecommerce->WERKS . "' AND \"Article\" = '" . $stock->SKU . "'
                            GROUP BY \"Article\", \"Location\"
                        
                            UNION ALL
                        
                            SELECT
                                MATNR AS \"Article\",
                                -SUM(BDMNG) AS \"CurrentStock\",
                                WERKS AS \"Location\"
                            FROM \"S4H_SLT_CAR\".\"RESB\"
                            WHERE \"WERKS\" = '" . $ecommerce->WERKS . "' AND \"MATNR\" = '" . $stock->SKU . "' AND (LGORT = '0001' OR LGORT = 'EC01')
                            GROUP BY MATNR, WERKS
                        
                            UNION ALL
                        
                            SELECT
                                MATNR AS \"Article\",
                                -SUM(VMENG) AS \"CurrentStock\",
                                WERKS AS \"Location\"
                            FROM \"S4H_SLT_CAR\".VBBE
                            WHERE \"WERKS\" = '" . $ecommerce->WERKS . "' AND \"MATNR\" = '" . $stock->SKU . "' AND (LGORT = '0001' OR LGORT = 'EC01')
                            GROUP BY MATNR, WERKS
                        ) AS Subquery
                        GROUP BY \"Article\", \"Location\"";

                    $result = odbc_fetch_array(odbc_exec($conn, $query));

                    if (!$result) {
                        Log::channel('woo-stocks')->error('SAP CAR query error: ' . $query);
                    }

                    if(intval($result['ZLABST']) >= 0 && intval($result['ZLABST']) != $stock->woo_stock) {
                        $newStock = Stock::where('id', $stock->id)->first();
                        if ($newStock !== null) {
                            $newStock->sap_stock = intval($result['ZLABST']);
                            $newStock->save();
                        }
                    }

                }

            }
           
            odbc_close($conn);
            
        } catch (\Exception $e) {
            Log::channel('woo-stocks')->error($e->getMessage());
        }

        $endTime = Carbon::now();
        $executionTime = $endTime->diffInSeconds($startTime);
        $formattedTime = sprintf("%02d:%02d", floor($executionTime / 60), $executionTime % 60);
        Log::channel('woo-stocks')->info('GET ' . $countTotal  . ' Product Stock: ' . $formattedTime);

        return Command::SUCCESS;
    }
}
