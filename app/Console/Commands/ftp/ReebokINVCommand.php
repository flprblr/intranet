<?php

namespace App\Console\Commands\ftp;

use Illuminate\Console\Command;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ReebokINVCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ftp:reebokinv';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envia Stock de Reebok';

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
        $sls = new ReebokINVCommand();

        $sls->ftpFunctionINV($fechaFin, $horaActual);

        $endTime = Carbon::now();
        $executionTime = $endTime->diffInSeconds($startTime);
        Log::channel('reebok')->info('Sync SAP->Sales Day: ' . $executionTime . ' seconds');

        return Command::SUCCESS;
    }

    public function ftpFunctionINV($fechaFin, $horaActual)
    {
        $sls_nombre1               = 'nombre1';
        $sls_centro                = 'centro';
        $sls_tipo_material         = 'tipo_material';
        $sls_grupo_articulos       = 'grupo_articulos';
        $sls_material              = 'material';
        $sls_texto_breve_material  = 'texto_breve_material';
        $sls_libre_utilizacion     = 'libre_utilizacion';
        $sls_transito              = 'transito';
        $sls_almacen               = 'almacen';
        $sls_costo                 = 'costo';
        $sls_costo_total           = 'costo_total' . "\n";

        $content = '';
        $sls_file_name = '';

        try {

            $sls = new ReebokINVCommand();

            $dsn = env('HANA_DSN');
            $user = env('HANA_USER');
            $password = env('HANA_PASSWORD');

            $connection = odbc_connect($dsn, $user, $password,);
            if (!$connection) {
                Log::channel('reebok')->error('SAP S/4HANA connection error.');
            }

            $sls_file_name  = "stock" . "_" . $sls->convertirFecha($fechaFin) . ".csv";

            //Log::channel('reebok')->info($sls_file_name);

            $content = $sls_nombre1 . ";" .
                $sls_centro  . ";" .
                $sls_tipo_material . ";" .
                $sls_grupo_articulos . ";" .
                $sls_material . ";" .
                $sls_texto_breve_material . ";" .
                $sls_libre_utilizacion . ";" .
                $sls_transito . ";" .
                $sls_almacen . ";" .
                $sls_costo . ";" .
                $sls_costo_total;


            // Variables para cláusula WHERE
            $condiciones = array();
            $condiciones[] = "a.sptag <=  '" . $fechaFin . "'";
            $condiciones[] = "b.matkl in ('RB')";
            $condiciones[] = "a.lgort in ('0001','0003','1000','EC01','MP01')";


            // Construye la cláusula WHERE
            $clausula_where = "";

            if (count($condiciones) > 0) {
                $clausula_where = " WHERE " . implode(" AND ", $condiciones);
            }

            $query = "SELECT 
                        c.name1 AS name1, 
                        a.werks AS werks, 
                        b.mtart AS mtart, 
                        b.matkl AS matkl, 
                        a.matnr AS matnr, 
                        d.maktx AS maktx, 
                        SUM((1 * a.mzubb) - (a.magbb * 1)) AS labst, 
                        0 AS vmuml, 
                        a.lgort AS lgort,
                        e.verpr * 100 AS verpr  
                        FROM sapabap1.s031      AS a 
                        JOIN sapabap1.mara      AS b ON (a.matnr = b.matnr) 
                        JOIN sapabap1.t001w     AS c ON (a.werks = c.werks) 
                        JOIN sapabap1.makt      AS d ON (b.matnr = d.matnr) 
                        LEFT JOIN sapabap1.mbew AS e ON ( b.matnr = e.matnr AND c.bwkey = e.bwkey ) 
                      $clausula_where
                      GROUP BY c.name1, a.werks, b.mtart, b.matkl, 
                               a.matnr, d.maktx, a.lgort, e.verpr  
                      ORDER BY a.werks, a.lgort, a.matnr";

            $result2 = odbc_exec($connection, $query);

            while ($row = odbc_fetch_array($result2)) {
                $content .= $row['NAME1'] . ";";
                $content .= $row['WERKS'] . ";";
                $content .= $row['MTART'] . ";";
                $content .= $row['MATKL'] . ";";
                $content .= $row['MATNR'] . ";";
                $content .= $row['MAKTX'] . ";";
                $content .= $sls->eliminarDecimales($row['LABST']) . ";";
                $content .= $row['VMUML'] . ";";
                $content .= $row['LGORT'] . ";";
                $content .= $sls->eliminarDecimales($row['VERPR']) . ";";
                $content .= $sls->eliminarDecimales($row['VERPR']) * $sls->eliminarDecimales($row['LABST']) . "\n";
            }

            $content = utf8_encode($content);

            //Log::channel('reebok')->info($content);
            $filename = $sls_file_name;

            //$directory = "/home/sftp/report/reebok/";

            if (empty($content) || empty($filename)) {
                Log::channel('reebok')->error('El contenido o el nombre del archivo está vacío. No se envió nada al FTP.');
            }
            try {
                $result = Storage::disk('reebok')->put($filename, $content);

                if ($result) {
                    //Log::channel('reebok')->info('Archivo ' . $filename . ' almacenado con éxito en el FTP en el directorio ' );
                } else {
                    Log::channel('reebok')->error('Hubo un problema al guardar el archivo ' . $filename . ' en el FTP en el directorio ' );
                }
            } catch (\Exception $e) {
                Log::channel('reebok')->error('Error al intentar guardar en el FTP: ' . $e->getMessage());
            }
        } catch (\Exception $e) {
            Log::channel('reebok')->error($e->getMessage());
        }
    }

    function convertirFecha($fechaOriginal)
    {
        $anio = substr($fechaOriginal, 0, 4);
        $mes = substr($fechaOriginal, 4, 2);
        $dia = substr($fechaOriginal, 6, 2);

        return $dia . $mes . $anio;
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
