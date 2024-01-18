<?php

namespace App\Console\Commands\ftp;

use Illuminate\Console\Command;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class AdidasSLSCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ftp:adidas';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envia Venta de Adidas';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $startTime = Carbon::now();

        $fechaInicio = Carbon::now()->subDays(8)->format('Ymd');
        $fechaFin = Carbon::now()->subDay()->format('Ymd');
        $horaActual = Carbon::now()->format('His');
        $sls = new AdidasSLSCommand();

        $sls->ftpFunctionSLS($fechaInicio, $fechaFin, $horaActual);

        $endTime = Carbon::now();
        $executionTime = $endTime->diffInSeconds($startTime);
        Log::channel('adidas')->info('FTP Adidas: ' . $executionTime . ' seconds');

        return Command::SUCCESS;
    }

    public function ftpFunctionSLS($fechaInicio, $fechaFin, $horaActual)
    {
        $sls_tienda          = 'tienda';
        $sls_fecha_documento = 'fecha_documento';
        $sls_nro_documento   = 'nro_documento';
        $sls_codigo_articulo = 'codigo_articulo';
        $sls_nombre_articulo = 'nombre_articulo';
        $sls_talla           = 'talla';
        $sls_cantidad        = 'cantidad';
        $sls_valor_total     = 'valor_total';
        $sls_centro          = 'centro';
        $sls_canal           = 'canal';
        $sls_costo           = 'costo';
        $sls_ean             = 'ean' . "\n";

        $content = '';
        $sls_file_name = '';

        try {

            $sls = new AdidasSLSCommand();

            $dsn = env('HANA_DSN');
            $user = env('HANA_USER');
            $password = env('HANA_PASSWORD');

            $connection = odbc_connect($dsn, $user, $password);
            if (!$connection) {
                Log::channel('adidas')->error('SAP S4 connection error.');
            }

            $sls_file_name  = "ventas" . "_" . $sls->convertirFecha($fechaFin) . "_" . $horaActual . ".csv";

            // Log::channel('adidas')->info($sls_file_name);

            $content = $sls_tienda . ";" . $sls_fecha_documento  . ";" . $sls_nro_documento . ";" . $sls_codigo_articulo . ";" . $sls_nombre_articulo . ";" . $sls_talla . ";" . $sls_cantidad . ";" . $sls_valor_total . ";" .  $sls_centro . ";" . $sls_canal . ";" . $sls_costo . ";" . $sls_ean;

            // Variables para cláusula WHERE
            $condiciones = array();
            $condiciones[] = "a.fkdat between '" . $fechaInicio . "' and '" . $fechaFin . "'";;
            //$condiciones[] = "a.fkdat between '20231120' and '20231126'";
            $condiciones[] = "a.matkl in  ('AD','AN')";

            // Construye la cláusula WHERE
            $clausula_where = "";

            if (count($condiciones) > 0) {
                $clausula_where = " WHERE " . implode(" AND ", $condiciones);
            }

            $query = "SELECT     a.name1, --Nombre tienda
                                 a.fkdat, --Fecha
                                 substring( a.xblnr, 5, 12 ) AS xblnr, --Nro. Documento sin DTE
                                 a.matnr, --Material
                                 a.arktx, --Nombre Material
                                 b.size1, --Talla
                                 a.fkimg, --Cantidad
                                 a.kzwi1, --Valor Total
                                 a.werks, --Centro
                                 a.vtweg, --Canal de distribucion  
                                 c.verpr * a.fkimg as verpr, --Costo * Cantidad
                                 b.ean11  --Ean                                                                                      
                from sapabap1.ZDDL_MRGN_SDP  as a 
                join sapabap1.mara AS b ON a.matnr = b.matnr
                left join sapabap1.zddl_costs as c on a.fkdat = c.fecha and a.werks = c.werks and a.matnr = c.matnr
                $clausula_where
                order by a.werks, a.fkdat, a.xblnr";

            $result2 = odbc_exec($connection, $query);

            while ($row = odbc_fetch_array($result2)) {
                $content .= $row['NAME1'] . ";";
                $content .= $sls->formatoFecha($row['FKDAT']) . ";";
                $content .= $row['XBLNR'] . ";";
                $content .= $row['MATNR'] . ";";
                $content .= $row['ARKTX'] . ";";
                $content .= $row['SIZE1'] . ";";
                $content .= $sls->eliminarDecimales($row['FKIMG']) . ";";
                $content .= $sls->eliminarPunto($row['KZWI1']) . ";";
                $content .= $row['WERKS'] . ";";
                $content .= $row['VTWEG'] . ";";
                $content .= $sls->eliminarDecimales($row['VERPR']) . ";";
                $content .= $row['EAN11'] . "\n";
            }

            $content = utf8_encode($content);

            //Log::channel('adidas')->info($content);
            $filename = $sls_file_name;

            //$directory = "/home/sftp/report/adidas/";

            if (empty($content) || empty($filename)) {
                Log::channel('adidas')->error('El contenido o el nombre del archivo está vacío. No se envió nada al FTP.');
            }
            try {
                //$result = Storage::disk('sftp')->put($directory . $filename, $content);
                $result = Storage::disk('adidas')->put($filename, $content);

                if ($result) {
                    // Log::channel('adidas')->info('Archivo ' . $filename . ' almacenado con éxito en el FTP en el directorio ' . $directory);
                } else {
                    Log::channel('adidas')->error('Hubo un problema al guardar el archivo ' . $filename . ' en el FTP en el directorio ');
                }
            } catch (\Exception $e) {
                Log::channel('adidas')->error('Error al intentar guardar en el FTP: ' . $e->getMessage());
            }
        } catch (\Exception $e) {
            Log::channel('adidas')->error($e->getMessage());
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
        return "$dia-$mes-$anio";
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
