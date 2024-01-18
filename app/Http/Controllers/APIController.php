<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;


class APIController extends Controller
{ 
    // https://intranet.ynk.cl/api/salescard/{society}/{salesOrg}/{channel}
    public function salesCard($society, $salesOrg, $channel) {

        $sales = [];

        // lastYearDay
        $from = Carbon::now()->subYear()->addDay()->format('Y-m-d');
        $to = Carbon::now()->subYear()->addDay()->format('Y-m-d');
        $cache = 86400;
        $result = $this->getSalesByDate($cache, $from, $to, $society, $salesOrg, $channel);
        $lastYearDay = array("lastYearDay" => $result);
        $sales = array_merge($sales, $lastYearDay);
        // end lastYearDay

        // actualDay
        $from = Carbon::now()->format('Y-m-d');
        $to = Carbon::now()->format('Y-m-d');
        $cache = 60;
        $result = $this->getSalesByDate($cache, $from, $to, $society, $salesOrg, $channel);
        $actualDay = array("actualDay" => $result);
        $sales = array_merge($sales, $actualDay);
        // end actualDay
    
        // lastYearMonth
        $from = Carbon::now()->startOfMonth()->subYear()->format('Y-m-d');
        $to = Carbon::now()->subYear()->format('Y-m-d');
        $cache = 86400;
        $result = $this->getSalesByDate($cache, $from, $to, $society, $salesOrg, $channel);
        $lastYearMonth = array("lastYearMonth" => $result);
        $sales = array_merge($sales, $lastYearMonth);
        // end lastYearMonth
    
        // actualMonth
        $from = Carbon::now()->startOfMonth()->format('Y-m-d');
        $to = Carbon::now()->format('Y-m-d');
        $cache = 60;
        $result = $this->getSalesByDate($cache, $from, $to, $society, $salesOrg, $channel);
        $actualMonth = array("actualMonth" => $result);
        $sales = array_merge($sales, $actualMonth);
        // end actualMonth
    
        // lastYearYear
        $from = Carbon::now()->startOfYear()->subYear()->format('Y-m-d');
        $to = Carbon::now()->subYear()->format('Y-m-d');
        $cache = 86400;
        $result = $this->getSalesByDate($cache, $from, $to, $society, $salesOrg, $channel);
        $lastYearYear = array("lastYearYear" => $result);
        $sales = array_merge($sales, $lastYearYear);
        // end lastYearYear
    
        // actualYear
        $from = Carbon::now()->startOfYear()->format('Y-m-d');
        $to = Carbon::now()->format('Y-m-d');
        $cache = 60;
        $result = $this->getSalesByDate($cache, $from, $to, $society, $salesOrg, $channel);
        $actualYear = array("actualYear" => $result);
        $sales = array_merge($sales, $actualYear);
        // end actualYear

        return $sales;
    }

    protected function getSalesByDate($cache, $from, $to, $society, $salesOrg, $channel) {

        $cacheKey = "getSalesByDate_{$from}_{$to}_{$society}_{$salesOrg}_{$channel}";
    
        return Cache::remember($cacheKey, $cache, function () use ($from, $to, $society, $salesOrg, $channel) {

            $query = "SELECT SUM(SALE) AS total_sales FROM sales WHERE FKDAT BETWEEN ? AND ?";
            $params = array($from, $to);
    
            if ($society != 'ALL') {
                $query .= " AND BUKRS = ?";
                $params[] = $society;
            }
            if ($salesOrg != 'ALL') {
                $query .= " AND VKORG = ?";
                $params[] = $salesOrg;
            }
            if ($channel != 'ALL') {
                $query .= " AND VTWEG = ?";
                $params[] = $channel;
            }
    
            $result = DB::select($query, $params);
    
            return intval($result[0]->total_sales);
        });
    }


    // https://intranet.ynk.cl/api/saleschartday/{society}/{salesOrg}/{channel}
    public function salesChartDay($society, $salesOrg, $channel) {

        $sales = [];

        // actualYearDays
        $from = Carbon::now()->subDays(15)->format('Y-m-d');
        $to = Carbon::now()->format('Y-m-d');
        $cache = 60;
        $actualYearDays = $this->getSalesByDay($cache, $from, $to, $society, $salesOrg, $channel);
        $sales[] = $actualYearDays;
        // actualYearDays

        // lastYearDays
        $from = Carbon::now()->subYear()->subDays(14)->format('Y-m-d');
        $to = Carbon::now()->subYear()->addDays(16)->format('Y-m-d');
        $cache = 86400;
        $lastYearDays = $this->getSalesByDay($cache, $from, $to, $society, $salesOrg, $channel);
        $sales[] = $lastYearDays;
        // lastYearDays

        // twoYearsAgo
        $from = Carbon::now()->subYear(2)->subDays(13)->format('Y-m-d');
        $to = Carbon::now()->subYear(2)->addDays(17)->format('Y-m-d');
        $cache = 86400;
        $twoYearsAgoDays = $this->getSalesByDay($cache, $from, $to, $society, $salesOrg, $channel);
        $sales[] = $twoYearsAgoDays;
        // twoYearsAgo

        return $sales;
    }

    protected function getSalesByDay($cache, $from, $to, $society, $salesOrg, $channel) {
        $cacheKey = "getSalesByDay_{$from}_{$to}_{$society}_{$salesOrg}_{$channel}";
    
        return Cache::remember($cacheKey, $cache, function () use ($from, $to, $society, $salesOrg, $channel) {
            // Subconsulta para generar la serie de fechas
            $dateSeriesQuery = "
                SELECT DATE(ADDDATE('1970-01-01', t4.i * 10000 + t3.i * 1000 + t2.i * 100 + t1.i * 10 + t0.i)) AS date
                FROM (SELECT 0 i UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) t0,
                     (SELECT 0 i UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) t1,
                     (SELECT 0 i UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) t2,
                     (SELECT 0 i UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) t3,
                     (SELECT 0 i UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) t4
                WHERE DATE(ADDDATE('1970-01-01', t4.i * 10000 + t3.i * 1000 + t2.i * 100 + t1.i * 10 + t0.i)) BETWEEN DATE(?) AND DATE(?)
            ";
    
            // Consulta SQL para obtener ventas
            $salesQuery = "SELECT d.date, COALESCE(SUM(s.SALE), 0) AS TOTAL
                           FROM ($dateSeriesQuery) d
                           LEFT JOIN sales s ON d.date = s.FKDAT";
            $salesParams = [$from, $to];
    
            // Agregar condiciones adicionales a la consulta SQL
            if ($society != 'ALL') {
                $salesQuery .= " AND s.BUKRS = ?";
                $salesParams[] = $society;
            }
            if ($salesOrg != 'ALL') {
                $salesQuery .= " AND s.VKORG = ?";
                $salesParams[] = $salesOrg;
            }
            if ($channel != 'ALL') {
                $salesQuery .= " AND s.VTWEG = ?";
                $salesParams[] = $channel;
            }
    
            $salesQuery .= " GROUP BY d.date ORDER BY d.date";
    
            // Ejecutar la consulta SQL
            $result = DB::select($salesQuery, $salesParams);
    
            // Preparar los resultados manteniendo la estructura de $sales[]
            $sales = [];
            foreach ($result as $row) {
                $sales[] = $row->TOTAL;
            }
    
            return $sales;
        });
    }
    
    


    // https://intranet.ynk.cl/api/saleschartmonth/{society}/{salesOrg}/{channel}
    public function salesChartMonth($society, $salesOrg, $channel) {

        $sales = [];

        // actualYearMonths
        $from = Carbon::now()->firstOfYear()->format('Ymd');
        $to = Carbon::now()->endOfMonth()->format('Ymd');
        $cache = 60;
        $actualYearMonths = $this->getSalesByMonth($cache, $from, $to, $society, $salesOrg, $channel);
        $sales[] = $actualYearMonths;
        // actualYearMonths

        // lastYearMonths
        $from = Carbon::now()->subYear()->firstOfYear()->format('Ymd');
        $to = Carbon::now()->subYear()->endOfYear()->format('Ymd');
        $cache = 31536000;
        $lastYearMonths = $this->getSalesByMonth($cache, $from, $to, $society, $salesOrg, $channel);
        $sales[] = $lastYearMonths;
        // lastYearMonths

        // twoYearsAgoMonths
        $from = Carbon::now()->subYear(2)->firstOfYear()->format('Ymd');
        $to = Carbon::now()->subYear(2)->endOfYear()->format('Ymd');
        $cache = 31536000;
        $twoYearsAgoMonths = $this->getSalesByMonth($cache, $from, $to, $society, $salesOrg, $channel);
        $sales[] = $twoYearsAgoMonths;
        // twoYearsAgoMonths

        return $sales;
    }

    protected function getSalesByMonth($cache, $from, $to, $society, $salesOrg, $channel) {

        $cacheKey = "getSalesByMonth_{$from}_{$to}_{$society}_{$salesOrg}_{$channel}";

        return Cache::remember($cacheKey, $cache, function () use ($from, $to, $society, $salesOrg, $channel) {

            $sales = [];
            $query = "SELECT SUM(SALE) AS TOTAL
                        FROM sales
                        WHERE FKDAT BETWEEN ? AND ?";
            $params = [$from, $to];

            if ($society != 'ALL') {
                $query .= " AND BUKRS = ?";
                $params[] = $society;
            }
            if ($salesOrg != 'ALL') {
                $query .= " AND VKORG = ?";
                $params[] = $salesOrg;
            }
            if ($channel != 'ALL') {
                $query .= " AND VTWEG = ?";
                $params[] = $channel;
            }

            $query = $query . " GROUP BY MONTH(FKDAT) ORDER BY MONTH(FKDAT)";
        
            $result = DB::select($query, $params);

            foreach($result as $row) {
                $sales[] = $row->TOTAL;
            }
            
            return $sales; 
        });
    }


    // https://intranet.ynk.cl/api/salesranking/{society}/{salesOrg}/{channel}
    public function salesRanking($society, $salesOrg, $channel) {

        $from = Carbon::now()->subDays(30)->format('Y-m-d');
        $to = Carbon::now()->format('Y-m-d');

        $cacheKey = "salesRanking_{$from}_{$to}_{$society}_{$salesOrg}_{$channel}";
        $cache = 60;
    
        return Cache::remember($cacheKey, $cache, function () use ($from, $to, $society, $salesOrg, $channel) {

            $query = "SELECT 
            s.NAME1 AS STORE, 
            SUM(CASE WHEN s.FKDAT = CURRENT_DATE THEN s.SALE ELSE 0 END) AS SALES_TODAY,
            SUM(CASE WHEN s.FKDAT BETWEEN CURRENT_DATE - INTERVAL '30' DAY AND CURRENT_DATE THEN s.SALE ELSE 0 END) AS SALES_LAST_30_DAYS
          FROM 
            sales s
          WHERE 
            s.FKDAT BETWEEN CURRENT_DATE - INTERVAL '30' DAY AND CURRENT_DATE";

            $params = array();

            if ($society != 'ALL') {
                $query .= " AND s.BUKRS = ?";
                $params[] = $society;
            }
            if ($salesOrg != 'ALL') {
                $query .= " AND s.VKORG = ?";
                $params[] = $salesOrg;
            }
            if ($channel != 'ALL') {
                $query .= " AND s.VTWEG = ?";
                $params[] = $channel;
            }

            $query .= " GROUP BY s.NAME1 ORDER BY 2 DESC";

            $result = DB::select($query, $params);

            return $result;

        });

    }

}
