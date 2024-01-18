<?php

namespace App\Console\Commands\ftp;

use Illuminate\Console\Command;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ShoppertrackSLSCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ftp:shoppertrack';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envia Venta de ShopperTrack';

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
        $sls = new ShoppertrackSLSCommand();

        $sls->ftpFunctionSLS($fechaInicio, $fechaFin, $horaActual);

        $endTime = Carbon::now();
        $executionTime = $endTime->diffInSeconds($startTime);
        Log::channel('shoppertrack')->info('FTP Shoppertrack: ' . $executionTime . ' seconds');

        return Command::SUCCESS;
    }

    public function ftpFunctionSLS($fechaInicio, $fechaFin, $horaActual)
    {
        $content = '';
        $sls_file_name = '';

        try {

            $sls = new ShoppertrackSLSCommand();

            $dsn = env('HANA_DSN');
            $user = env('HANA_USER');
            $password = env('HANA_PASSWORD');

            $connection = odbc_connect($dsn, $user, $password,);
            if (!$connection) {
                Log::channel('shoppertrack')->error('SAP S4 connection error.');
            }

            $sls_file_name  = "SALES" . "_" . $sls->convertirFecha($fechaFin) . ".txt";

            //Log::channel('shoppertrack')->info($sls_file_name);

            // Variables para cláusula WHERE
            $condiciones = array();
            $condiciones[] = "a.fkdat = '" . $fechaFin . "'";
            //$condiciones[] = "a.fkdat between '20230807' and '20230813'";
            $condiciones[] = "a.vkorg in ('8000')";
            $condiciones[] = "a.werks in ('8000','8001','8002','8003',
                                          '8005','8006','8007','8008',
                                          '8009','8010','8011','8012',
                                          '8013','8014','8021','8023',
                                          '8026','8027')";
            $condiciones[] = "a.vtweg in ('01')";

            // Construye la cláusula WHERE
            $clausula_where = "";

            if (count($condiciones) > 0) {
                $clausula_where = " WHERE " . implode(" AND ", $condiciones);
            }

            $query = "SELECT c.name_org3, a.fkdat, e.erzet, sum( a.kzwi1 ) as kzwi1,
                             a.vbeln, sum( a.fkimg ) as fkimg, 
                             SUBSTRING(a.mtart, 2) as mtart,
                             e.waerk,
                             case 
                             when b.operacion = '+' then '1' 
                             else '0' 
                             end as num1,
                             a.shkzg
                      from sapabap1.zddl_mrgn_sdp as a 
                      join sapabap1.zclsd_tb_fact as b on a.fkart = b.fkart
                      join sapabap1.t001w         as d on a.werks = d.werks 
                      join sapabap1.but000        as c on d.kunnr = c.partner 
                      LEFT JOIN sapabap1.vbrk     AS e ON a.vbeln = e.vbeln 
                      $clausula_where
                      group by c.name_org3, a.fkdat, e.erzet, a.vbeln,
                               a.mtart, e.waerk, b.operacion, a.shkzg
                               
                      UNION ALL
                      
                      select c.name_org3, a.fkdat, e.erzet, sum( a.kzwi1 ) as kzwi1,
                             a.vbeln, sum( case 
                                           when a.shkzg = 'X' then a.fkimg * -1 
                                           else a.fkimg 
                                           end ) as fkimg ,
                             'TOT' as mtart, e.waerk,
                             case 
                             when b.operacion = '+' then '1' 
                             else '0' 
                             end as num1,
                             'T'
                      from sapabap1.zddl_mrgn_sdp as a 
                      join sapabap1.zclsd_tb_fact as b on a.fkart = b.fkart
                      join sapabap1.t001w         as d on a.werks = d.werks 
                      join sapabap1.but000        as c on d.kunnr = c.partner 
                      LEFT JOIN sapabap1.vbrk     AS e ON a.vbeln = e.vbeln 
                      $clausula_where
                      group by c.name_org3, a.fkdat, e.erzet, a.vbeln, e.waerk,
                      case 
                      when b.operacion = '+' then '1' 
                      else '0' 
                      end
                      order by c.name_org3, a.fkdat, e.erzet, a.vbeln, mtart";

            $result2 = odbc_exec($connection, $query);

            while ($row = odbc_fetch_array($result2)) {
                $content .= $row['NAME_ORG3'] . ",";
                $content .= $sls->formatoFecha($row['FKDAT']) . ",";
                $content .= $sls->formatoHora($row['ERZET']) . ",";
                $content .= $sls->eliminarPunto($row['KZWI1']) . ",";
                $content .= $row['VBELN'] . ",";
                $content .= $sls->eliminarDecimales($row['FKIMG']) . ",";
                $content .= $row['MTART'] . ",";
                $content .= $row['WAERK'] . ",";
                $content .= $row['NUM1'] .  "\n";
            }

            $content = utf8_encode($content);

            //Log::channel('shoppertrack')->info($content);
            $filename = $sls_file_name;

            //$directory = "/home/sftp/report/shoppertrack/";

            if (empty($content) || empty($filename)) {
                Log::channel('shoppertrack')->error('El contenido o el nombre del archivo está vacío. No se envió nada al FTP.');
            }
            try {
                $result = Storage::disk('shoppertrack')->put($filename, $content);

                if ($result) {
                    // Log::channel('shoppertrack')->info('Archivo ' . $filename . ' almacenado con éxito en el FTP en el directorio ' . $directory);
                } else {
                    Log::channel('shoppertrack')->error('Hubo un problema al guardar el archivo ' . $filename . ' en el FTP en el directorio ');
                }
            } catch (\Exception $e) {
                Log::channel('shoppertrack')->error('Error al intentar guardar en el FTP: ' . $e->getMessage());
            }
        } catch (\Exception $e) {
            Log::channel('shoppertrack')->error($e->getMessage());
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
        return "$anio$mes$dia";
    }

    function formatoHora($hora)
    {
        // Desglosar Hora
        $horas = substr($hora, 0, 2);
        $minutos = substr($hora, 2, 2);
        $segundos = substr($hora, 4, 2);

        // Formatear
        return "$horas$minutos$segundos";
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
