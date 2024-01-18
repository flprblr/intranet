<?php

namespace App\Console\Commands\ftp;

use Illuminate\Console\Command;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class GfkSLSCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ftp:gfk';

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

        $fecha = Carbon::now()->subDay()->format('Ymd');
        $horaActual = Carbon::now()->format('His');
        $sls = new GfkSLSCommand();

        $sls->ftpFunctionSLS($fecha, $horaActual);

        $endTime = Carbon::now();
        $executionTime = $endTime->diffInSeconds($startTime);
        Log::channel('gfk')->info('Sync SAP->Sales Day: ' . $executionTime . ' seconds');

        return Command::SUCCESS;
    }

    public function ftpFunctionSLS($fecha, $horaActual)
    {
        $sls_fecha           = 'fecha';
        $sls_sucursal        = 'sucursal';
        $sls_material        = 'material';
        $sls_descr_producto  = 'descripcion_producto';
        $sls_id_jerar1       = 'id_jerarquia_1';
        $sls_jerar1          = 'jerarquia_1';
        $sls_id_jerar2       = 'id_jerarquia_2';
        $sls_jerar2          = 'jerarquia_2';
        $sls_id_jerar3       = 'id_jerarquia_3';
        $sls_jerar3          = 'jerarquia_3';
        $sls_subcategoria    = 'subcategoria';
        $sls_descr_categoria = 'descripcion_subcategoria';
        $sls_proveedor       = 'proveedor';
        $sls_marca           = 'marca';
        $sls_model_descr     = 'descripcion_modelo';
        $sls_ean             = 'ean';
        $sls_tipo_venta      = 'tipo_venta';
        $sls_indicadores     = 'indicadores';
        $sls_sellers         = 'sellers';
        $sls_venta           = 'venta';
        $sls_unidades        = 'unidades' . "\n";

        $content = '';
        $sls_file_name = '';

        try {

            $sls = new GfkSLSCommand();

            $dsn = env('HANA_DSN');
            $user = env('HANA_USER');
            $password = env('HANA_PASSWORD');

            $connection = odbc_connect($dsn, $user, $password,);
            if (!$connection) {
                Log::channel('gfk')->error('SAP S4 connection error.');
            }

            $sls_file_name  = "ventas" . "_" . $sls->convertirFecha($fecha) . "_" . $horaActual . ".csv";

            //Log::channel('gfk')->info($sls_file_name);

            $content = $sls_fecha . ";" .
                       $sls_sucursal  . ";" .
                       $sls_material . ";" .
                       $sls_descr_producto . ";" .
                       $sls_id_jerar1 . ";" .
                       $sls_jerar1 . ";" .
                       $sls_id_jerar2  . ";" .
                       $sls_jerar2 . ";" .
                       $sls_id_jerar3 . ";" .
                       $sls_jerar3 . ";" .
                       $sls_subcategoria . ";" .
                       $sls_descr_categoria  . ";" .
                       $sls_proveedor . ";" .
                       $sls_marca . ";" .
                       $sls_model_descr . ";" .
                       $sls_ean . ";" .
                       $sls_tipo_venta  . ";" .
                       $sls_indicadores . ";" .
                       $sls_sellers . ";" .
                       $sls_venta . ";" .
                       $sls_unidades;

            // Variables para cláusula WHERE
            $condiciones = array();
            $condiciones[] = "a.fecha = '" . $fecha . "'";
            //$condiciones[] = "a.fecha = '20231002'";
            $condiciones[] = "a.vkorg = '8000' ";
            $condiciones[] = "f.relif = 'X' ";

            // Construye la cláusula WHERE
            $clausula_where = "";

            if (count($condiciones) > 0) {
                $clausula_where = " WHERE " . implode(" AND ", $condiciones);
            }

            $query = "SELECT a.fecha, b.name1, c.matnr, c.maktx, 
                            substring( c.prodh,1,5 )  AS ID_PRI, c.pri, 
                            substring( c.prodh,1,10 ) AS ID_SEG, c.seg, 
                            substring( c.prodh,1,15 ) AS ID_TER, c.ter, 
                            d.mvgr1, e.bezei, g.name1 AS NAME2, c.wgbez,
                            c.maktx, c.ean11,
                            CASE WHEN a.vtweg = '01' THEN ''
                                 WHEN a.vtweg = '02' THEN '1P'
                                 WHEN a.vtweg = '05' THEN '2P'
                                 ELSE ''
                            END AS TIPO_VENTA,
                            CASE WHEN a.vtweg = '01' THEN 'Venta de retail en canal propio fisico'
                                 WHEN a.vtweg = '02' THEN 'Venta de retail en el canal propio digital'
                                 WHEN a.vtweg = '05' THEN 'Venta propia en el marketplace'
                                 ELSE ''
                            END AS INDICADORES,
                            h.mc_name1, a.total, a.cantidad
                     FROM sapabap1.zddl_sales      AS a
                     JOIN sapabap1.t001w           AS b ON a.werks = b.werks
                     JOIN sapabap1.zddl_materiales AS c ON a.matnr = c.matnr
                     LEFT JOIN sapabap1.mvke       AS d ON a.matnr = d.matnr AND a.vkorg = d.vkorg AND a.vtweg = d.vtweg 
                     LEFT JOIN sapabap1.tvm1t      AS e ON d.mvgr1 = e.mvgr1 AND e.spras = 'S'
                     LEFT JOIN sapabap1.eina       AS f ON c.pmatn = f.matnr OR c.matnr = f.matnr 
                     LEFT JOIN sapabap1.lfa1       AS g ON f.lifnr = g.lifnr 
                     LEFT JOIN sapabap1.but000     AS h ON a.kunnr =  h.partner 
                     $clausula_where
                     GROUP BY a.fecha, b.name1, c.matnr, c.maktx, c.prodh,
                              c.pri, c.seg, c.ter, d.mvgr1, e.bezei,
                              g.name1, c.wgbez, c.maktx, c.ean11, a.werks,
                              a.vtweg, h.mc_name1, a.total, a.cantidad
                     ORDER BY a.werks, a.fecha";

            $result2 = odbc_exec($connection, $query);

            while ($row = odbc_fetch_array($result2)) {
                $content .= $sls->formatoFecha($row['FECHA']) . ";";
                $content .= $row['NAME1'] . ";";
                $content .= $row['MATNR'] . ";";
                //$content .= $row['MAKTX'] . ";";
                $content .= str_replace(array('"', '(',')'), '', $row['MAKTX']) . ";";
                $content .= $row['ID_PRI'] . ";";
                $content .= $row['PRI'] . ";";
                $content .= $row['ID_SEG'] . ";";
                $content .= $row['SEG'] . ";";
                $content .= $row['ID_TER'] . ";";
                $content .= $row['TER'] . ";";
                $content .= $row['MVGR1'] . ";";
                $content .= $row['BEZEI'] . ";";
                $content .= $row['NAME2'] . ";";
                $content .= $row['WGBEZ'] . ";";
                $content .= $row['MAKTX'] . ";";
                $content .= $row['EAN11'] . ";";
                $content .= $row['TIPO_VENTA'] . ";";
                $content .= $row['INDICADORES'] . ";";
                $content .= $row['MC_NAME1'] . ";";
                $content .= $sls->eliminarPunto($row['TOTAL']) . ";";
                $content .= $sls->eliminarDecimales($row['CANTIDAD']) . "\n";
            }

            $content = utf8_encode($content);

            //Log::channel('gfk')->info($content);
            $filename = $sls_file_name;

            //$directory = "/home/sftp/report/gfk/";

            if (empty($content) || empty($filename)) {
                Log::channel('gfk')->error('El contenido o el nombre del archivo está vacío. No se envió nada al FTP.');
            }
            try {
                $result = Storage::disk('gfk')->put($filename, $content);

                if ($result) {
                    // Log::channel('gfk')->info('Archivo ' . $filename . ' almacenado con éxito en el FTP en el directorio ' . $directory);
                } else {
                    Log::channel('gfk')->error('Hubo un problema al guardar el archivo ' . $filename . ' en el FTP en el directorio ');
                }
            } catch (\Exception $e) {
                Log::channel('gfk')->error('Error al intentar guardar en el FTP: ' . $e->getMessage());
            }
        } catch (\Exception $e) {
            Log::channel('gfk')->error($e->getMessage());
        }
    }

    function convertirFecha($fechaOriginal)
    {
        $anio = substr($fechaOriginal, 0, 4);
        $mes = substr($fechaOriginal, 4, 2);
        $dia = substr($fechaOriginal, 6, 2);

        return $dia . $mes . $anio;
    }

    function formatoFecha($fecha)
    {
        // Desglosar fecha
        $anio = substr($fecha, 0, 4);
        $mes = substr($fecha, 4, 2);
        $dia = substr($fecha, 6, 2);

        // Formatear
        return "$anio-$mes-$dia";
    }

    function eliminarPunto($cadena)
    {
        return str_replace(['.'], '', $cadena);
    }

    function eliminarDecimales($numero)
    {
        $posicionPunto = strpos($numero, '.');

        if ($posicionPunto !== false) {
            return substr($numero, 0, $posicionPunto);
        } else {
            return $numero;
        }
    }
}
