<?php

namespace App\Console\Commands\ftp;

use Illuminate\Console\Command;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class PumaSLSCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ftp:puma';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envia Venta de Puma';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $startTime = Carbon::now();

        $fechaInicio = Carbon::now()->subDays(7)->format('Ymd');
        $fechaFin = Carbon::now()->subDay()->format('Ymd');
        $horaActual = Carbon::now()->format('His');
        $sls = new PumaSLSCommand();

        $sls->ftpFunctionSLS($fechaInicio, $fechaFin, $horaActual);

        $endTime = Carbon::now();
        $executionTime = $endTime->diffInSeconds($startTime);
        Log::channel('puma')->info('FTP Puma: ' . $executionTime . ' seconds');

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
        $sls_centro          = 'centro'. "\n";

        $content = '';
        $sls_file_name = '';

        try {

            $sls = new PumaSLSCommand();

            $dsn = env('HANA_DSN');
            $user = env('HANA_USER');
            $password = env('HANA_PASSWORD');

            $connection = odbc_connect($dsn, $user, $password,);
            if (!$connection) {
                Log::channel('puma')->error('SAP S4 connection error.');
            }

            $sls_file_name  = "ventas.csv";

            // Log::channel('puma')->info($sls_file_name);

            $content = $sls_tienda . ";" . $sls_fecha_documento  . ";" . $sls_nro_documento . ";" . $sls_codigo_articulo . ";" . $sls_nombre_articulo . ";" . $sls_talla . ";" . $sls_cantidad . ";" . $sls_valor_total . ";" .  $sls_centro;

            // Variables para cláusula WHERE
            $condiciones = array();
            $condiciones[] = "a.fkdat between '" . $fechaInicio . "' and '" . $fechaFin . "'";
            //$condiciones[] = "a.fkdat between '20230721' and '20230815'";
            $condiciones[] = "a.matkl in  ('PM')";

            // Construye la cláusula WHERE
            $clausula_where = "";

            if (count($condiciones) > 0) {
                $clausula_where = " WHERE " . implode(" AND ", $condiciones);
            }

            $query = "SELECT a.name1, --Nombre tienda
                                 a.fkdat, --Fecha
                                 substring( a.xblnr, 5, 12 ) AS xblnr, --Nro. Documento sin DTE
                                 a.matnr, --Material
                                 a.arktx, --Nombre Material
                                 b.size1, --Talla
                                 a.fkimg, --Cantidad
                                 a.kzwi1, --Valor Total
                                 a.werks --Centro                                                                                             
                from sapabap1.ZDDL_MRGN_SDP  as a 
                join sapabap1.mara AS b ON a.matnr = b.matnr
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
                $content .= $row['WERKS'] . "\n";
            }

            $content = utf8_encode($content);

            // Log::channel('puma')->info($content);
            $filename = $sls_file_name;

            //$directory = "/home/sftp/report/puma/";

            if (empty($content) || empty($filename)) {
                Log::channel('puma')->error('El contenido o el nombre del archivo está vacío. No se envió nada al FTP.');
            }
            try {
                $result = Storage::disk('puma')->put($filename, $content);

                if ($result) {
                    // Log::channel('puma')->info('Archivo ' . $filename . ' almacenado con éxito en el FTP en el directorio ' . $directory);
                } else {
                    Log::channel('puma')->error('Hubo un problema al guardar el archivo ' . $filename . ' en el FTP en el directorio ');
                }
            } catch (\Exception $e) {
                Log::channel('puma')->error('Error al intentar guardar en el FTP: ' . $e->getMessage());
            }
        } catch (\Exception $e) {
            Log::channel('puma')->error($e->getMessage());
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
