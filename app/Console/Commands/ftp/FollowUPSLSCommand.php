<?php

namespace App\Console\Commands\ftp;

use Illuminate\Console\Command;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class FollowUPSLSCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ftp:followup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envia Venta de FollowUP';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $startTime = Carbon::now();

        $fechaFin = Carbon::now()->subDay()->format('Ymd');
        $horaActual = Carbon::now()->format('His');
        $sls = new FollowUPSLSCommand();

        $sls->ftpFunctionSLS($fechaFin, $horaActual);

        $endTime = Carbon::now();
        $executionTime = $endTime->diffInSeconds($startTime);
        Log::channel('followup')->info('FTP FollowUp: ' . $executionTime . ' seconds');

        return Command::SUCCESS;
    }

    public function ftpFunctionSLS($fechaFin, $horaActual)
    {
        $sls_nro_documento = 'nro_documento';
        $sls_c_pos_id      = 'c_pos_id';
        $sls_taxid         = 'taxid';
        $sls_fecha         = 'fecha';
        $sls_m_product_id  = 'm_product_id';
        $sls_valor_total   = 'valor_total' . "\n";

        $content = '';
        $sls_file_name = '';

        try {

            $sls = new FollowUPSLSCommand();

            $dsn = env('HANA_DSN');
            $user = env('HANA_USER');
            $password = env('HANA_PASSWORD');

            $connection = odbc_connect($dsn, $user, $password);
            if (!$connection) {
                Log::channel('followup')->error('SAP S4 connection error.');
            }

            $sls_file_name  = "ventas" . "_" . $sls->convertirFecha($fechaFin) . "_" . $horaActual . ".csv";

            //Log::channel('followup')->info($sls_file_name);

            $content = $sls_nro_documento . "," .
                $sls_c_pos_id  . "," .
                $sls_taxid . "," .
                $sls_fecha . "," .
                $sls_m_product_id . "," .
                $sls_valor_total;

            // Variables para cláusula WHERE
            $condiciones = array();
            //$condiciones[] = "a.fkdat = '" . $fechaFin . "'";
            $condiciones[] = "a.fkdat = '20231020'";
            $condiciones[] = "a.werks not in ('1068','2019','3006','5001',
                                              '6103','7003','7004','7301',
                                              '7400','7500','AB10','AB20',
                                              'AB30','AB50','AB70','AB71',
                                              'AB73','AB74','AB75','AB80',
                                              'BEC1','BEC2','DX01','CEC1',
                                              'CR01','EEC1','OK01','OL02')";
            $condiciones[] = "a.vkorg not in ('8000')";
            $condiciones[] = "a.fkart not in ('ZFMY','ZIV','ZMV1','ZMV2',
                                              'ZMV3','ZMV4')";

            // Construye la cláusula WHERE
            $clausula_where = "";

            if (count($condiciones) > 0) {
                $clausula_where = " WHERE " . implode(" AND ", $condiciones);
            }

            $query = "SELECT substring( a.xblnr, 5, 12 ) AS xblnr, --Nro. Documento sin DTE
                             a.werks, --Centro
                             replace( d.rut, '.', '' ) AS rut,  -- RUT sin puntos
                             a.fkdat, --Fecha
                             b.erzet, -- Hora
                             a.matnr, --Material
                             a.fkimg, --Cantidad
                             a.kzwi1                                                                                              
                      FROM sapabap1.zddl_mrgn_sdp       AS a
                      LEFT JOIN sapabap1.vbrk           AS b ON a.vbeln = b.vbeln
                      LEFT JOIN sapabap1.zddl_vtas_com  AS c ON a.vbeln = c.vbeln AND a.posnr = c.posnr
                      LEFT JOIN sapabap1.zbuk_employees AS d ON c.pernr = d.pernr
                $clausula_where
                order by a.werks, a.fkdat, a.xblnr";

            $result2 = odbc_exec($connection, $query);

            while ($row = odbc_fetch_array($result2)) {
                $content .= $row['XBLNR'] . ",";
                $content .= $row['WERKS'] . ",";
                $content .= $row['RUT'] . ",";
                $content .= $sls->formatoFecha($row['FKDAT']) . " " . $sls->formatoHora($row['ERZET']) . ",";
                $content .= $row['MATNR'] . ",";
                $content .= $sls->eliminarDecimales($row['FKIMG']) . ",";
                $content .= $sls->eliminarPunto($row['KZWI1']) . "\n";
            }

            $content = utf8_encode($content);

            //Log::channel('followup')->info($content);
            $filename = $sls_file_name;

            //$directory = "/home/sftp/report/followup/";

            if (empty($content) || empty($filename)) {
                Log::channel('followup')->error('El contenido o el nombre del archivo está vacío. No se envió nada al FTP.');
            }
            try {
                $result = Storage::disk('followup')->put($filename, $content);

                if ($result) {
                    // Log::channel('followup')->info('Archivo ' . $filename . ' almacenado con éxito en el FTP en el directorio ' . $directory);
                } else {
                    Log::channel('followup')->error('Hubo un problema al guardar el archivo ' . $filename . ' en el FTP en el directorio ');
                }
            } catch (\Exception $e) {
                Log::channel('followup')->error('Error al intentar guardar en el FTP: ' . $e->getMessage());
            }
        } catch (\Exception $e) {
            Log::channel('followup')->error($e->getMessage());
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

    function formatoHora($hora)
    {
        // Desglosar Hora
        $horas = substr($hora, 0, 2);
        $minutos = substr($hora, 2, 2);
        $segundos = substr($hora, 4, 2);

        // Formatear
        return "$horas:$minutos:$segundos";
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
