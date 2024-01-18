<?php

namespace App\Console\Commands\ftp;

use Illuminate\Console\Command;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Models\ZappleTbCorre;
use LengthException;

class NikeSLSCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ftp:nike';

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
        $sls = new NikeSLSCommand();
        
        //dia especifico
       /* $fecha = Carbon::now();
        $fecha->setDate(2023, 9, 19); // Año, Mes, Día 
        $sls->ftpFunctionSLS($fecha->format('Ymd'), $fecha->format('ymd'));*/
        #$sls->ftpFunctionINV($fechaSAP, $fechaControl);*

        $dias = 173;

        for ($i = 1; $i <= $dias; $i++) {

            //$fechaSAP = Carbon::now()->subDays($i)->format('Ymd');
            $fechaSAP = Carbon::createFromDate(2023, 9, 4)->subDays($i)->format('Ymd');
            $fechaControl = Carbon::createFromDate(2023, 9, 4)->subDays($i)->format('ymd');
            //$fechaControl = Carbon::now()->addDays($i)->format('ymd');

            $sls->ftpFunctionSLS($fechaSAP, $fechaControl);
            $sls->ftpFunctionINV($fechaSAP, $fechaControl);
        }


        $endTime = Carbon::now();
        $executionTime = $endTime->diffInSeconds($startTime);
        Log::channel('nike')->info('FTP Nike: ' . $executionTime . ' seconds');

        return Command::SUCCESS;
    }


    public function ftpFunctionSLS($fechaSAP, $fechaControl)
    {

        try {


            $sls = new NikeSLSCommand();
            $dsn = env('HANA_DSN');
            $user = env('HANA_USER');
            $password = env('HANA_PASSWORD');

            $connection = odbc_connect($dsn, $user, $password,);
            if (!$connection) {
                Log::channel('nike')->error('SAP S4 connection error.');
            }

            $query_shipto = "SELECT a.werks,a.shipto 
            from sapabap1.znike_werks as a
            join sapabap1.t001w as b on ( a.werks = b.werks )
            where a.werks = '6101'
            order by a.werks";

            $result_shipto = odbc_exec($connection, $query_shipto);

            // Recorre shipto
            while ($row_shipto = odbc_fetch_array($result_shipto)) {

                $sls_file_name  = "Ventas" . "_" . "Nike" . "_" . $row_shipto['SHIPTO'] . "_"  . $sls->convertirFecha($fechaSAP) . ".txt";

                // Log::channel('nike')->info($sls_file_name);

                $content = '';
                // Variables para cláusula WHERE
                $condiciones = array();
                $condiciones[] = "a.fkdat between '" . $fechaSAP . "' and '" . $fechaSAP . "'";
                $condiciones[] = "a.matkl in  ('NI','JR')";
                $condiciones[] = "a.matnr not in ('NI954561023','NI95456102300L','NI95456102300M','NI95456102300S','NI9545610230XL')";

                // Construye la cláusula WHERE
                $clausula_where = "";

                if (count($condiciones) > 0) {
                    $clausula_where = " WHERE " . implode(" AND ", $condiciones);
                }

                $query = "SELECT g.shipto, a.fkdat, b.erzet, a.vbeln, a.matnr, a.pmatn,                                                    
                sum(a.fkimg) as sold_quantity,                                                       
                sum(a.kzwi1 ) as sold_amount, 
                f.verpr as cost, 
                a.size1 , a.pltyp                                               
                from sapabap1.ZDDL_MRGN_SDP  as a 
                join sapabap1.vbrk as b on (a.vbeln = b.vbeln )
                join sapabap1.zclsd_tb_fact as c on  ( a.fkart = c.fkart )
                join sapabap1.mbew as f on ( a.matnr = f.matnr and a.werks = f.bwkey )
                join sapabap1.znike_werks as g on ( a.werks = g.werks )
                $clausula_where
                and a.werks  = '" . $row_shipto['WERKS'] . "'
                group by g.shipto,a.fkdat,b.erzet,a.vbeln,a.matnr,a.pmatn,f.verpr,a.size1,a.pltyp
                having sum( case when a.shkzg = 'X' or c.operacion = 'X' then  a.fkimg * -1 else  a.fkimg end  ) <> 0
                order by g.shipto,a.fkdat,a.matnr";

                $result2 = odbc_exec($connection, $query);
                while ($row = odbc_fetch_array($result2)) {
                    $content .= '"' . $row['SHIPTO'] . '"' . ";";
                    $content .= '"' . $sls->formatoFechaHora($row['FKDAT'], $row['ERZET']) . '"' . ";";
                    $content .= '"B"' . ";";
                    $content .= '"' . $row['VBELN'] . '"' . ";";
                    $content .= '"' . $sls->transformarSKU($row['MATNR']) . '"' . ";";
                    $content .= '"' . str_replace(',', '.', $row['SIZE1'])   . '"' . ";";
                    $content .=  intval($row['SOLD_QUANTITY']) . ";";
                    $content .=  $sls->eliminarPuntoYComa($row['COST']) . ";";
                    $content .=  $sls->eliminarPuntoYComa($row['SOLD_AMOUNT']) . ";";
                    $content .= '"E"' . ";";
                    $content .=  0 . "\n";
                }
                // Log::channel('nike')->info($content);
                $filename = $sls_file_name;

                //$directory = "/home/sftp/report/nike/";

                if (empty($content) || empty($filename)) {
                    Log::channel('nike')->error('El contenido o el nombre del archivo está vacío. No se envió nada al FTP.');
                }
                try {
                    $result = Storage::disk('nike')->put($filename, $content);

                    if ($result) {
                        // Log::channel('nike')->info('Archivo ' . $filename . ' almacenado con éxito en el FTP en el directorio ' . $directory);
                    } else {
                        Log::channel('nike')->error('Hubo un problema al guardar el archivo ' . $filename . ' en el FTP en el directorio ');
                    }
                } catch (\Exception $e) {
                    Log::channel('nike')->error('Error al intentar guardar en el FTP: ' . $e->getMessage());
                }
            }
        } catch (\Exception $e) {
            Log::channel('nike')->error($e->getMessage());
        }
    }

    public function ftpFunctionINV($fechaSAP, $fechaControl)
    {

        try {


            $sls = new NikeSLSCommand();
            $dsn = env('HANA_DSN');
            $user = env('HANA_USER');
            $password = env('HANA_PASSWORD');

            $connection = odbc_connect($dsn, $user, $password,);
            if (!$connection) {
                Log::channel('nike')->error('SAP S4 connection error.');
            }

            $query_shipto = "SELECT a.werks,a.shipto 
            from sapabap1.znike_werks as a
            join sapabap1.t001w as b on ( a.werks = b.werks )
            where a.werks = '6101'
            order by a.werks";


            $result_shipto = odbc_exec($connection, $query_shipto);

            // Recorre shipto
            while ($row_shipto = odbc_fetch_array($result_shipto)) {

                $sls_file_name  = "Stock" . "_" . "Nike" . "_" . $row_shipto['SHIPTO'] . "_"  . $sls->convertirFecha($fechaSAP) . ".txt";

                // Log::channel('nike')->info($sls_file_name);

                $content = '';
                // Variables para cláusula WHERE
                $condiciones = array();
                $condiciones[] = "a.sptag <=  '" . $fechaSAP . "' ";
                $condiciones[] = "b.matkl in  ('NI','JR')";
                $condiciones[] = "a.matnr not in ('NI954561023','NI95456102300L','NI95456102300M','NI95456102300S','NI9545610230XL')";
                // Construye la cláusula WHERE
                $clausula_where = "";

                if (count($condiciones) > 0) {
                    $clausula_where = " WHERE " . implode(" AND ", $condiciones);
                }

                $query = "SELECT a.matnr, g.shipto, b.size1, sum( ( 1 * a.mzubb ) - ( a.magbb * 1 ) ) as stock , f.verpr  as cost                              
                from sapabap1.s031  as a
                join sapabap1.mara  as b on ( a.matnr = b.matnr )
                join sapabap1.makt  as c on ( a.matnr = c.matnr and c.spras = 'S' )
                join sapabap1.t001w as d on ( a.werks = d.werks )
                join sapabap1.mbew  as f on ( a.matnr = f.matnr and a.werks = f.bwkey )
                join sapabap1.znike_werks as g on ( a.werks = g.werks )
                $clausula_where 
                and a.werks  = '" . $row_shipto['WERKS'] . "'
                group by
                a.matnr,g.shipto,b.size1,f.verpr
                having sum( ( 1 * a.mzubb ) - ( a.magbb * 1 ) ) <> 0 
                order by a.matnr, g.shipto";

                // Log::channel('nike')->info($query);


                $result2 = odbc_exec($connection, $query);
                while ($row = odbc_fetch_array($result2)) {

                    $content .= '"' . $row['SHIPTO'] . '"' . ";";
                    $content .= '"' . $sls->transformarSKU($row['MATNR']) . '"' . ";";
                    $content .= '"' . str_replace(',', '.', $row['SIZE1'])   . '"' . ";";
                    $content .=  intval($row['STOCK']) . ";";
                    $content .=  $sls->eliminarPuntoYComa($row['COST']) . "\n";
                }
                // Log::channel('nike')->info($content);

                $filename = $sls_file_name;

                //$directory = "/home/sftp/report/nike/";

                if (empty($content) || empty($filename)) {
                    Log::channel('nike')->error('El contenido o el nombre del archivo está vacío. No se envió nada al FTP.');
                }
                try {
                    $result = Storage::disk('nike')->put($filename, $content);

                    if ($result) {
                        // Log::channel('nike')->info('Archivo ' . $filename . ' almacenado con éxito en el FTP en el directorio ' . $directory);
                    } else {
                        Log::channel('nike')->error('Hubo un problema al guardar el archivo ' . $filename . ' en el FTP en el directorio ');
                    }
                } catch (\Exception $e) {
                    Log::channel('nike')->error('Error al intentar guardar en el FTP: ' . $e->getMessage());
                }
            }
        } catch (\Exception $e) {
            Log::channel('nike')->error($e->getMessage());
        }
    }

    function formatoFechaHora($fecha, $hora)
    {
        // Desglosar fecha
        $anio = substr($fecha, 0, 4);
        $mes = substr($fecha, 4, 2);
        $dia = substr($fecha, 6, 2);

        // Desglosar hora
        $horas = substr($hora, 0, 2);
        $minutos = substr($hora, 2, 2);
        $segundos = substr($hora, 4, 2);

        // Formatear
        return "$dia/$mes/$anio $horas:$minutos:$segundos";
    }

    function transformarSKU($sku)
    {
        // Recortar los primeros dos caracteres y los tres últimos
        $resultadoParcial = substr($sku, 2, -3);

        // Extraer los últimos 3 caracteres del resultado parcial
        $ultimosTres = substr($resultadoParcial, -3);

        // Recortar los últimos 3 caracteres del resultado parcial
        $resto = substr($resultadoParcial, 0, -3);

        // Unir con un guion y devolver
        return $resto . '-' . $ultimosTres;
    }

    function convertirFecha($fechaOriginal)
    {
        $anio = substr($fechaOriginal, 0, 4);
        $mes = substr($fechaOriginal, 4, 2);
        $dia = substr($fechaOriginal, 6, 2);

        return $dia . '_' . $mes . '_' . $anio;
    }
    function eliminarPuntoYComa($cadena)
    {
        return str_replace(['.', ','], '', $cadena);
    }
}
