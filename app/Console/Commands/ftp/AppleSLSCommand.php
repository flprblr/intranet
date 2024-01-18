<?php

namespace App\Console\Commands\ftp;

use Illuminate\Console\Command;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Models\ZappleTbCorre;
use LengthException;

class AppleSLSCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ftp:apple';

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
        $sls = new AppleSLSCommand();


        //dia especifico
        /* $fecha = Carbon::now();
        $fecha->setDate(2023, 9, 19); // Año, Mes, Día 
        $sls->ftpFunctionSLS($fecha->format('Ymd'), $fecha->format('ymd'));*/
        #$sls->ftpFunctionINV($fechaSAP, $fechaControl);*

        #Rangos
        $dias = 1; // dias hacia atras
        for ($i = 1; $i <= $dias; $i++) {
            $fechaSAP = Carbon::now()->subDays($i)->format('Ymd');
            $fechaControl = Carbon::now()->subDays($i)->format('ymd');
            $sls->ftpFunctionSLS($fechaSAP, $fechaControl);
            $sls->ftpFunctionINV($fechaSAP, $fechaControl);
        }


        $endTime = Carbon::now();
        $executionTime = $endTime->diffInSeconds($startTime);
        //Log::info('FTP Apple: ' . $executionTime . ' seconds');
        Log::channel('apple')->info('FTP Apple: ' . $executionTime . ' seconds');

        return Command::SUCCESS;
    }

    public function ftpFunctionSLS($fechaSAP, $fechaControl)
    {
        $sls_ctrl           = 'CTRL';
        $sls_sender_id      = '3422346';
        $sls_receiver_id    = '060704780001000';
        $sls_sendername     = 'YANEKEN';
        $sls_countrycode    = 'CL';
        $sls_source         = 'UTF-8';
        $sls_hdr            = 'HDR';
        $sls_reporting_date = '';
        $sls_location_id    = '';

        $sls_region         = 'AM';
        $sls_type           = 'SLS';
        $sls_guion          = '_';
        $sls_extension      = '.txt';
        $report_type        = 'SLS';


        // Log::info($fechaSAP);

        $content = '';
        $sls_file_name = '';

        try {
            $dsn = env('HANA_DSN');
            $user = env('HANA_USER');
            $password = env('HANA_PASSWORD');

            $connection = odbc_connect($dsn, $user, $password,);
            if (!$connection) {
                //Log::error('SAP S4 connection error.');
                Log::channel('apple')->error('SAP S4 connection error.');
            }


            #zapple_tb_corre

            $correlativoEntry = ZappleTbCorre::where('fecha', $fechaSAP)
                ->where('tipo', $report_type)
                ->first();

            $correlativo = 1;  // Valor por defecto

            if ($correlativoEntry && $correlativoEntry->correlativo) {
                $correlativo = intval($correlativoEntry->correlativo) + 1;

                // Actualizar el correlativo en la base de datos
                $correlativoEntry->correlativo = $correlativo;
                $correlativoEntry->save();
            } else {
                // Insertar el nuevo correlativo en la base de datos
                ZappleTbCorre::create([
                    'correlativo' => $correlativo,
                    'fecha' => $fechaSAP,
                    'tipo' => $report_type
                ]);
            }

            $sls_control_id = $fechaControl . sprintf('%02d', $correlativo);
            $sls_datetimestamp = Carbon::now()->format('YmdHis');

            $sls_file_name  = $sls_sender_id . $sls_guion . $sls_region . $sls_guion . $sls_type . $sls_guion . $sls_control_id . $sls_extension;
            // Log::info($sls_file_name);

            $content = $sls_ctrl . "\t" . $sls_sender_id  . "\t" . $sls_receiver_id . "\t" . $sls_type . "\t" . $sls_control_id . "\t" . $sls_datetimestamp . "\t" . $sls_sendername . "\t" .  $sls_countrycode . "\t" . $sls_source . "\n";


            // Variables para cláusula WHERE
            $condiciones = array();
            $condiciones[] = "a.fkdat between '" . $fechaSAP . "' and '" . $fechaSAP . "'";
            $condiciones[] = "a.vkorg in ('8000')";
            $condiciones[] = "a.rfbsk =  'C'";
            $condiciones[] = "a.mtart not in ('ZDEM','ZMUE')";
            $condiciones[] = "a.spras in ('S')";
            $condiciones[] = "a.fkart <> 'S1'";
            $condiciones[] = "a.matkl =  'AP'";
            $condiciones[] = "a.matnr not in ('PACK2IP11BLKCHRGR','PACK2IP11YLLCHRGR','PACKIP11YLLCHRGR','PACKIP11BLKCHRGR','661-16751E','661-16751M','661-20337M','661-30373M','LZ661-25614M','LZ661-28482M','LZ661-28613M','LZ661-30080M','ZP661-16412M')";


            // Construye la cláusula WHERE
            $clausula_where = "";

            if (count($condiciones) > 0) {
                $clausula_where = " WHERE " . implode(" AND ", $condiciones);
            }

            $query = "SELECT a.werks as werks, f.name_org3  as name_org3
            from sapabap1.zddl_vtas_emp2 as a 
            join sapabap1.zclsd_tb_fact  as b  on ( a.fkart  =  b.fkart   ) 
            join sapabap1.but000         as f  on ( a.kunnr  =  f.partner ) 
            $clausula_where
            and a.vtweg != '02'
            group by a.werks , f.name_org3
            order by a.werks;";

            $result = odbc_exec($connection, $query);

            // Recorre tiendas
            while ($row = odbc_fetch_array($result)) {
                $sls_reporting_date = $fechaSAP;
                $sls_location_id    = $row['NAME_ORG3'];

                $content .= $sls_hdr . "\t" . $sls_reporting_date . "\t" . $sls_location_id . "\n";

                $query2 = "SELECT
                    'DTL' as DTL ,a.matnr as apmn,' ' as upc_code,' ' as jan_code,
                    sum( case when b.operacion = '+'  and a.shkzg <> 'X' then a.fkimg else 0 end ) as quantity_sold,
                    sum( case when b.operacion = '-'  or   a.shkzg = 'X' then a.fkimg else 0 end ) as quantity_returned,
                    ' ' as serial_number,
                    a.xblnr as invoice_number,a.posnr as invoice_position,a.fkdat as invoice_date,a.kunrg as end_customer_id,c.mc_name1 as end_customer_name,
                    g.street as end_customer_address,g.city1 as end_customer_city,g.city2 as end_customer_state,g.post_code1 as end_customer_zipcode,
                    g.country as end_customer_country,
                    case when c.bu_group = 'ZEMP' or  c.bu_group = 'ZHON' or  c.bu_group = 'ZINT' or  c.bu_group = 'ZNAC' or  c.bu_group = 'ZSUC'  then 'BB' else  'EN'  end as end_customer_type,
                    a.shkzg
                    from sapabap1.zddl_vtas_emp2 as a 
                    join sapabap1.zclsd_tb_fact as b  on ( a.fkart = b.fkart )
                    join sapabap1.but000 as c on ( a.kunrg = c.partner )
                    left join sapabap1.but020 as f on ( c.partner = f.partner )
                    left join sapabap1.adrc as g on ( f.addrnumber = g.addrnumber )
                    $clausula_where
                    and a.vtweg != '02'
                    and a.werks =  '" . $row['WERKS'] . "'
                    group by c.bu_group,a.xblnr,a.matnr,a.posnr,a.fkdat,a.kunrg,c.mc_name1,g.street,g.city1,g.city2,g.post_code1,g.country,a.shkzg
                    order by a.xblnr,a.posnr ";

                $result2 = odbc_exec($connection, $query2);
                while ($row = odbc_fetch_array($result2)) {
                    $content .= $row['DTL'] . "\t";
                    $content .= $row['APMN'] . "\t";
                    $content .= $row['UPC_CODE'] . "\t";
                    $content .= $row['JAN_CODE'] . "\t";
                    $content .= intval($row['QUANTITY_SOLD']) . "\t";
                    $content .= intval($row['QUANTITY_RETURNED']) . "\t";
                    $content .= $row['SERIAL_NUMBER'] . "\t";
                    $content .= "\t";
                    $content .= $row['INVOICE_NUMBER'] . "\t";
                    $content .= intval($row['INVOICE_POSITION']) . "\t";
                    $content .= intval($row['INVOICE_DATE']) . "\t";
                    $content .= intval($row['END_CUSTOMER_ID']) . "\t";
                    $content .= str_replace("\n", '', $row['END_CUSTOMER_NAME']) . "\t";
                    $content .= str_replace("\n", '', $row['END_CUSTOMER_ADDRESS']) . "\t";
                    $content .= str_replace("\n", '', $row['END_CUSTOMER_CITY']) . "\t";
                    $content .= str_replace("\n", '', $row['END_CUSTOMER_STATE']) . "\t";
                    $content .= str_replace("\n", '', $row['END_CUSTOMER_ZIPCODE']) . "\t";
                    $content .= str_replace("\n", '', $row['END_CUSTOMER_COUNTRY']) . "\t";
                    $content .= str_replace("\n", '', $row['END_CUSTOMER_TYPE']) . "\n";
                }
            }

            $sls_reporting_date = $fechaSAP;
            $sls_location_id    = '1408436';
            $content .= $sls_hdr . "\t" . $sls_reporting_date . "\t" . $sls_location_id . "\n";

            $query1 = "SELECT
                'DTL' as DTL,a.matnr as apmn,' ' as upc_code,' ' as jan_code,
                sum( case when b.operacion = '+'  and a.shkzg <> 'X' then a.fkimg else 0 end ) as quantity_sold,
                sum( case when b.operacion = '-'  or   a.shkzg = 'X' then a.fkimg else 0 end ) as quantity_returned,
                ' ' as serial_number,
                a.xblnr as invoice_number,a.posnr as invoice_position,a.fkdat as invoice_date,a.kunrg as end_customer_id,c.mc_name1 as end_customer_name,
                g.street as end_customer_address,g.city1 as end_customer_city,g.city2 as end_customer_state,g.post_code1 as end_customer_zipcode,
                g.country as end_customer_country,
                case when c.bu_group = 'ZEMP' or  c.bu_group = 'ZHON' or  c.bu_group = 'ZINT' or  c.bu_group = 'ZNAC' or  c.bu_group = 'ZSUC'  then 'BB' else  'EN'  end as end_customer_type,
                a.shkzg
                from sapabap1.zddl_vtas_emp2 as a 
                join sapabap1.zclsd_tb_fact as b  on ( a.fkart = b.fkart )
                join sapabap1.but000 as c on ( a.kunrg = c.partner )
                left join sapabap1.but020 as f on ( c.partner = f.partner )
                left join sapabap1.adrc as g on ( f.addrnumber = g.addrnumber )
                $clausula_where
                and a.vtweg =  '02'
                group by c.bu_group,a.xblnr,a.matnr,a.posnr,a.fkdat,a.kunrg,c.mc_name1,g.street,g.city1,g.city2,g.post_code1,g.country,a.shkzg
                order by a.xblnr,a.posnr ";

            $result1 = odbc_exec($connection, $query1);

            while ($row = odbc_fetch_array($result1)) {
                $content .= $row['DTL'] . "\t";
                $content .= $row['APMN'] . "\t";
                $content .= $row['UPC_CODE'] . "\t";
                $content .= $row['JAN_CODE'] . "\t";
                $content .= intval($row['QUANTITY_SOLD']) . "\t";
                $content .= intval($row['QUANTITY_RETURNED']) . "\t";
                $content .= $row['SERIAL_NUMBER'] . "\t";
                $content .= "\t";
                $content .= $row['INVOICE_NUMBER'] . "\t";
                $content .= intval($row['INVOICE_POSITION']) . "\t";
                $content .= intval($row['INVOICE_DATE']) . "\t";
                $content .= intval($row['END_CUSTOMER_ID']) . "\t";
                $content .= str_replace("\n", '', $row['END_CUSTOMER_NAME']) . "\t";
                $content .= str_replace("\n", '', $row['END_CUSTOMER_ADDRESS']) . "\t";
                $content .= str_replace("\n", '', $row['END_CUSTOMER_CITY']) . "\t";
                $content .= str_replace("\n", '', $row['END_CUSTOMER_STATE']) . "\t";
                $content .= str_replace("\n", '', $row['END_CUSTOMER_ZIPCODE']) . "\t";
                $content .= str_replace("\n", '', $row['END_CUSTOMER_COUNTRY']) . "\t";
                $content .= str_replace("\n", '', $row['END_CUSTOMER_TYPE']) . "\n";
            }

            $content = utf8_encode(str_replace('"', ' ', $content));

            //Log::info($content);

            odbc_close($connection);
        } catch (\Exception $e) {
            //Log::error($e->getMessage());
            Log::channel('apple')->error($e->getMessage());
        }



        $filename = $sls_file_name;

        //$directory = "/home/sftp/report/apple/";

        if (empty($content) || empty($filename)) {
            //Log::error('El contenido o el nombre del archivo está vacío. No se envió nada al FTP.');
            Log::channel('apple')->error('El contenido o el nombre del archivo está vacío. No se envió nada al FTP.');
            return;
        }
        try {
            $result = Storage::disk('apple')->put($filename, $content);

            if ($result) {
                //Log::info('Archivo ' . $filename . ' almacenado con éxito en el FTP en el directorio ' . $directory);
                //Log::channel('apple')->info('Archivo ' . $filename . ' almacenado con éxito en el FTP en el directorio ' . $directory);
            } else {
                //Log::error('Hubo un problema al guardar el archivo ' . $filename . ' en el FTP en el directorio ' . $directory);
                Log::channel('apple')->error('Hubo un problema al guardar el archivo ' . $filename . ' en el FTP en el directorio ');
            }
        } catch (\Exception $e) {
            //Log::error('Error al intentar guardar en el FTP: ' . $e->getMessage());
            Log::channel('apple')->error('Error al intentar guardar en el FTP: ' . $e->getMessage());
        }
    }

    public function ftpFunctionINV($fechaSAP, $fechaControl)
    {
        $sls_ctrl           = 'CTRL';
        $sls_sender_id      = '3422346';
        $sls_receiver_id    = '060704780001000';
        $sls_sendername     = 'YANEKEN';
        $sls_countrycode    = 'CL';
        $sls_source         = 'UTF-8';
        $sls_hdr            = 'HDR';
        $sls_reporting_date = '';
        $sls_location_id    = '';
        $sls_region         = 'AM';
        $sls_type           = 'INV';
        $sls_guion          = '_';
        $sls_extension      = '.txt';
        $report_type        = 'INV';


        // Log::info($fechaSAP);

        $content = '';
        $sls_file_name = '';

        try {
            $dsn = env('HANA_DSN');
            $user = env('HANA_USER');
            $password = env('HANA_PASSWORD');

            $connection = odbc_connect($dsn, $user, $password,);
            if (!$connection) {
                //Log::error('SAP S4 connection error.');
                Log::channel('apple')->error('SAP S4 connection error.');
            }


            #zapple_tb_corre

            $correlativoEntry = ZappleTbCorre::where('fecha', $fechaSAP)
                ->where('tipo', $report_type)
                ->first();

            $correlativo = 1;  // Valor por defecto

            if ($correlativoEntry && $correlativoEntry->correlativo) {
                $correlativo = intval($correlativoEntry->correlativo) + 1;

                // Actualizar el correlativo en la base de datos
                $correlativoEntry->correlativo = $correlativo;
                $correlativoEntry->save();
            } else {
                // Insertar el nuevo correlativo en la base de datos
                ZappleTbCorre::create([
                    'correlativo' => $correlativo,
                    'fecha' => $fechaSAP,
                    'tipo' => $report_type
                ]);
            }

            $sls_control_id = $fechaControl . sprintf('%02d', $correlativo);
            $sls_datetimestamp = Carbon::now()->format('YmdHis');

            $sls_file_name  = $sls_sender_id . $sls_guion . $sls_region . $sls_guion . $sls_type . $sls_guion . $sls_control_id . $sls_extension;
            // Log::info($sls_file_name);

            $content = $sls_ctrl . "\t" . $sls_sender_id  . "\t" . $sls_receiver_id . "\t" . $sls_type . "\t" . $sls_control_id . "\t" . $sls_datetimestamp . "\t" . $sls_sendername . "\t" .  $sls_countrycode . "\t" . $sls_source . "\n";


            $query = "SELECT d.werks , f.name_org3 
            from sapabap1.mard as a
            left join sapabap1.mara as e on ( a.matnr = e.matnr )
            left join sapabap1.mean as b on ( a.matnr = b.matnr and b.hpean = 'X' )
            left join sapabap1.NSDM_V_MARD as c on ( a.matnr = c.matnr and a.werks = c.werks and a.lgort = c.lgort )
            left join sapabap1.t001w as d on ( a.werks = d.werks )
            join sapabap1.but000 as f on ( d.kunnr = f.partner )
            where
            e.matkl in ('AP') and 
            ( c.labst > 0 or c.umlme > 0 ) and
            c.lgort <> ' ' and
            a.matnr not in ('PACK2IP11BLKCHRGR','PACK2IP11YLLCHRGR','PACKIP11YLLCHRGR','PACKIP11BLKCHRGR','661-16751E','661-16751M','661-20337M','661-30373M','LZ661-25614M','LZ661-28482M','LZ661-28613M','LZ661-30080M','ZP661-16412M') and 
            d.vkorg in ('8000')
            group by  d.werks , f.name_org3
            order by  d.werks";

            $result = odbc_exec($connection, $query);

            // Recorre tiendas
            while ($row = odbc_fetch_array($result)) {
                $sls_reporting_date = $fechaSAP;
                $sls_location_id    = $row['NAME_ORG3'];

                $content .= $sls_hdr . "\t" . $sls_reporting_date . "\t" . $sls_location_id . "\n";
                $query1 = "SELECT
                'DTL' as dtl,
                a.matnr as APMN,
                ' ' as upc_code,
                ' ' as jan_code,
                sum( case when a.lgort = '0001' or a.lgort = '1000' then  ( 1 * a.mzubb ) - ( a.magbb * 1 ) end ) as inv_free,
                0       as inv_demo,
                0       as inv_tran,
                sum( case when a.lgort = '0002' or a.lgort = '2000' or a.lgort = '3000' or a.lgort = '5000'  then  ( 1 * a.mzubb ) - ( a.magbb * 1 ) end ) as inv_nove,
                0       as inv_resv,
                0       as inv_back,
                0       as inv_reci
                from sapabap1.s031 as a
                join sapabap1.mara as b on ( a.matnr = b.matnr )
                where a.werks = '" . $row['WERKS'] . "'
                and b.matkl = 'AP'
                and b.mtart not in ('ZDEM','ZMUE') 
                and a.matnr not in ('PACK2IP11BLKCHRGR','PACK2IP11YLLCHRGR','PACKIP11YLLCHRGR','PACKIP11BLKCHRGR','661-16751E','661-16751M','661-20337M','661-30373M','LZ661-25614M','LZ661-28482M','LZ661-28613M','LZ661-30080M','ZP661-16412M') 
                and a.sptag <= '" . $fechaSAP . "'
                group by a.matnr
                order by a.matnr";

                $result1 = odbc_exec($connection, $query1);

                while ($row = odbc_fetch_array($result1)) {
                    $content .= $row['DTL'] . "\t";
                    $content .= $row['APMN'] . "\t";
                    $content .= "\t";
                    $content .= "\t";
                    $content .= intval($row['INV_FREE']) . "\t";
                    $content .= "\t";
                    $content .= "\t";
                    $content .= intval($row['INV_NOVE']) . "\t";
                    $content .= "\t";
                    $content .= "\t";
                    $content .= "\n";
                }
            }
            $content = utf8_encode(str_replace('"', ' ', $content));

            // Log::info($content);

            odbc_close($connection);
        } catch (\Exception $e) {
            Log::channel('apple')->error($e->getMessage());
        }

        $filename = $sls_file_name;

        //$directory = "/home/sftp/report/apple/";

        if (empty($content) || empty($filename)) {
            Log::channel('apple')->error('El contenido o el nombre del archivo está vacío. No se envió nada al FTP.');
            return;
        }
        try {
            $result = Storage::disk('apple')->put($filename, $content);

            if ($result) {
                //Log::info('Archivo ' . $filename . ' almacenado con éxito en el FTP en el directorio ' . $directory);
            } else {
                Log::channel('apple')->error('Hubo un problema al guardar el archivo ' . $filename . ' en el FTP en el directorio ');
            }
        } catch (\Exception $e) {
            Log::channel('apple')->error('Error al intentar guardar en el FTP: ' . $e->getMessage());
        }
    }
}
