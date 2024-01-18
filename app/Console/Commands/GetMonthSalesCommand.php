<?php

namespace App\Console\Commands;
use Illuminate\Console\Command;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

Use App\Models\Sale;

class GetMonthSalesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'get:sales:month';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get Sales from SAP S4 ODBC';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $startTime = Carbon::now();

        $dsn = env('HANA_DSN');
        $user = env('HANA_USER');
        $password = env('HANA_PASSWORD');

        $channels = ['01','02','03','05'];

        $banners = [
            ['name' => 'Belsport', 'bukrs' => 'BELS', 'vkorg' => '1000'],
            ['name' => 'Bold', 'bukrs' => 'BELS', 'vkorg' => '2000'],
            ['name' => 'K1', 'bukrs' => 'BELS', 'vkorg' => '3000'],
            ['name' => 'Drops', 'bukrs' => 'BELS', 'vkorg' => '5000'],
            ['name' => 'Outlets', 'bukrs' => 'BELS', 'vkorg' => '6000'],
            ['name' => 'Locker', 'bukrs' => 'BELS', 'vkorg' => '6100'],
            ['name' => 'Qs Rx', 'bukrs' => 'BELS', 'vkorg' => '7000'],
            ['name' => 'Antihuman', 'bukrs' => 'BELS', 'vkorg' => '7300'],
            ['name' => 'Saucony', 'bukrs' => 'BELS', 'vkorg' => '7400'],
            ['name' => 'Aufbau', 'bukrs' => 'BELS', 'vkorg' => '8000'],
            ['name' => 'Bamers', 'bukrs' => 'BAME', 'vkorg' => 'B000'],
            ['name' => 'Crocs', 'bukrs' => 'CROC', 'vkorg' => 'C000'],
            ['name' => 'Oakley', 'bukrs' => 'OAK1', 'vkorg' => 'D000'],
            ['name' => 'The Lab', 'bukrs' => 'OAK1', 'vkorg' => 'E000'],
            ['name' => 'Hoka', 'bukrs' => 'BELS', 'vkorg' => '7500'],
        ];

        try {

            $conn = odbc_connect($dsn, $user, $password);
            if (!$conn) {
                Log::channel('sales-monthly')->error('SAP S4 connection error.');
            }
            
            $date = Carbon::now()->startOfMonth();
            
            for ($i = 1; $i <= 31; $i++) {
                $dateSAP = $date->format('Ymd');
                foreach($banners as $banner) {
                    foreach($channels as $channel) {
                        if ($channel === '01') {
                            $query = "SELECT NAME1, COALESCE(SUM(a.KZWI1) * 100, 0) AS TOTAL
                                        FROM SAPABAP1.ZDDL_MRGN_SDP  as a
                                        JOIN SAPABAP1.VBRK as b on (a.vbeln = b.vbeln)
                                        WHERE a.FKART NOT IN('ZD01','ZIG','ZIV','ZN01','ZN02','ZN03','ZN04','ZN05','ZN06','ZN07','ZN08','ZN09','ZN0E','ZNCE','ZNCM')
                                        AND a.BUKRS NOT IN('DBEL')
                                        AND a.BUKRS = '" . $banner['bukrs'] . "' 
                                        AND a.VKORG = '" . $banner['vkorg'] . "' 
                                        AND a.FKDAT BETWEEN '$dateSAP' AND '$dateSAP'
                                        AND a.VTWEG = '" . $channel . "'
                                        AND b.VBELN_REF != 'error'
                                        GROUP BY a.NAME1
                                        ORDER BY a.NAME1";

                            $results = odbc_exec($conn, $query);

                            if ($results) {
                                while ($row = odbc_fetch_array($results)) {

                                    Sale::updateOrCreate(
                                        [
                                            'BUKRS' => $banner['bukrs'],
                                            'VKORG' => $banner['vkorg'],
                                            'VTWEG' => $channel,
                                            'NAME1' => utf8_encode($row['NAME1']),
                                            'FKDAT' => $dateSAP,
                                        ],
                                        [
                                            'BUKRS' => $banner['bukrs'],
                                            'VKORG' => $banner['vkorg'],
                                            'VTWEG' => $channel,
                                            'NAME1' => utf8_encode($row['NAME1']),
                                            'FKDAT' => $dateSAP,
                                            'SALE' => intval($row['TOTAL'])
                                        ]
                                    );

                                    // Log::channel('sales-monthly')->info($banner['bukrs'] .'-'. $banner['vkorg'] .'-'. $channel .'-'. $date .'-'. $row['NAME1'] .'-'. intval($row['TOTAL']));
                                }
                            }
                            else {
                                Log::channel('sales-monthly')->error('SAP S4 query error: ' . $query);
                            }
                        }
                        else {
                            $query = "SELECT COALESCE(SUM(a.KZWI1) * 100, 0) AS TOTAL
                                        FROM SAPABAP1.ZDDL_MRGN_SDP  as a
                                        JOIN SAPABAP1.VBRK as b on (a.vbeln = b.vbeln)
                                        WHERE a.FKART NOT IN('ZD01','ZIG','ZIV','ZN01','ZN02','ZN03','ZN04','ZN05','ZN06','ZN07','ZN08','ZN09','ZN0E','ZNCE','ZNCM')
                                        AND a.BUKRS NOT IN('DBEL')
                                        AND a.BUKRS = '" . $banner['bukrs'] . "' 
                                        AND a.VKORG = '" . $banner['vkorg'] . "' 
                                        AND a.FKDAT BETWEEN '$dateSAP' AND '$dateSAP'
                                        AND a.VTWEG = '" . $channel . "'
                                        AND b.VBELN_REF != 'error' ";
                            $result = odbc_fetch_array(odbc_exec($conn, $query));
                            if (!$result) {
                                Log::channel('sales-monthly')->error('SAP S4 query error: ' . $query);
                            }
                            Sale::updateOrCreate(
                                [
                                    'BUKRS' => $banner['bukrs'],
                                    'VKORG' => $banner['vkorg'],
                                    'VTWEG' => $channel,
                                    'NAME1' => '-',
                                    'FKDAT' => $dateSAP,
                                ],
                                [
                                    'BUKRS' => $banner['bukrs'],
                                    'VKORG' => $banner['vkorg'],
                                    'VTWEG' => $channel,
                                    'NAME1' => '-',
                                    'FKDAT' => $dateSAP,
                                    'SALE' => intval($result['TOTAL'])
                                ]
                            );
                        }
                    }
                }
                $date->addDay();
            }
            odbc_close($conn);
        } catch (\Exception $e) {
            Log::channel('sales-monthly')->error($e->getMessage());
        }

        $endTime = Carbon::now();
        $executionTime = $endTime->diffInSeconds($startTime);
        Log::channel('sales-monthly')->info('GET SAP S4 Sales Month: ' . $executionTime . ' seconds');

        return Command::SUCCESS;
    }
}
