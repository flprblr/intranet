<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class PosInformationController extends Controller
{
    public function index()
    {

        $organizacion = [
            '0' => [
                'name' => 'Belsport',
                'acronym' => 'bels',
            ],
            '1' => [
                'name' => 'IGS',
                'acronym' => 'igs',
            ],
        ];

        $names = [
            'BELS', 'BOLD', 'KONE', 'QSRX', 'DROPS', 'SAUCONY', 'APPLE', 'AH',
            'OUTLET', 'THELAB', 'BAMERS', 'OAKLEY', 'CROCS',
        ];

        $banners = array_map(function ($name) {
            return ['name' => $name];
        }, $names);

        $fechaActual = Carbon::now();
        $fechaMenos7Dias = Carbon::now()->subDays(7);

        return Inertia::render('PosReport/SalesPos', compact('organizacion', 'banners'));
    }

    public function search(Request $request)
    {

        $organizacion = [
            '0' => [
                'name' => 'Belsport',
                'acronym' => 'bels',
            ],
            '1' => [
                'name' => 'IGS',
                'acronym' => 'igs',
            ],
        ];

        $names = [
            'BELS', 'BOLD', 'KONE', 'QSRX', 'DROPS', 'SAUCONY', 'APPLE', 'AH',
            'OUTLET', 'THELAB', 'BAMERS', 'OAKLEY', 'CROCS',
        ];

        $banners = array_map(function ($name) {
            return ['name' => $name];
        }, $names);


        $fechaActual = Carbon::now();

        if (empty($request->dateStart) && empty($request->dateEnd) && empty($request->banner)) {

            $result = DB::connection($request->org)->select("
            SELECT 
                a.fecha_emision AS Fecha_documento,
                to_char(a.creado, 'HH24:MI:SS') AS hora, 
                a.nro_documento AS folio_boleta, 
                a.estado_docto,
                e.name AS tienda,
                TRIM(LEADING '0' FROM e.m_warehouse_id_sap) AS warehouse_id_sap, 
                d.sku AS codigo_articulo, 
                d.description AS descripcion_articulo,
                SUM(c.cantidad) AS unidades_articulo,
                TO_CHAR(CAST(c.valor_unitario AS INTEGER), 'L999G999G999') AS Monto_venta,
                a.mail_cliente AS email_cliente,
                f.descripcion AS tipo_documento,
                h.value AS cod_vendedor,
                i.taxid AS rut_vendedor,
                x.name AS vendedor,
                h.name as cajero
            FROM pos.pos_c_documento AS a
            INNER JOIN pos.pos_c_detalledocumento AS c ON a.c_documento_id = c.c_documento_id
            JOIN pos.pos_m_producto AS d ON d.m_product_id = c.m_product_id
            JOIN pos.pos_m_warehouse AS e ON e.m_warehouse_id = c.m_warehouse_id
            JOIN pos.pos_c_tipodocumento AS f ON a.c_tipodocumento_id = f.c_tipodocumento_id
            JOIN pos.pos_ad_org AS g ON g.ad_org_id_sap = e.m_warehouse_id_sap
            JOIN pos.pos_ad_user AS h on a.ad_user_id = h.ad_user_id
            JOIN pos.pos_ad_user AS x on c.ad_user_id = x.ad_user_id
            JOIN pos.pos_c_bpartner AS i on i.c_bpartner_id = h.c_bpartner_id
            WHERE a.mail_cliente IS NOT NULL 
                AND a.fecha_emision BETWEEN '" . $fechaActual . "' AND '" . $fechaActual . "'
                AND g.ventas not in ('ECOMMERCE')
	            AND d.description not in ('GASTOS DE ENVIO')
            GROUP BY 
                a.fecha_emision,
                to_char(a.creado, 'HH24:MI:SS'),
                a.nro_documento,
                a.estado_docto,
                e.name,
                e.m_warehouse_id_sap,
                d.sku,
                d.description,
                c.valor_unitario,
                a.mail_cliente,
                f.descripcion,
                h.name,
                h.value,
                i.taxid,
                h.name,
                x.name
                ORDER BY a.fecha_emision, to_char(a.creado, 'HH24:MI:SS') ASC;           
        ");
        } elseif (empty($request->banner)) {

            $result = DB::connection($request->org)->select("
            SELECT 
                a.fecha_emision AS Fecha_documento,
                to_char(a.creado, 'HH24:MI:SS') AS hora,
                a.nro_documento AS folio_boleta, 
                a.estado_docto,
                e.name AS tienda,
                TRIM(LEADING '0' FROM e.m_warehouse_id_sap) AS warehouse_id_sap, 
                d.sku AS codigo_articulo, 
                d.description AS descripcion_articulo,
                SUM(c.cantidad) AS unidades_articulo,
                TO_CHAR(CAST(c.valor_unitario AS INTEGER), 'L999G999G999') AS Monto_venta,
                a.mail_cliente AS email_cliente,
                f.descripcion AS tipo_documento,
                h.value AS cod_vendedor,
                i.taxid AS rut_vendedor,
                x.name AS vendedor,
                h.name as cajero
            FROM pos.pos_c_documento AS a
            INNER JOIN pos.pos_c_detalledocumento AS c ON a.c_documento_id = c.c_documento_id
            JOIN pos.pos_m_producto AS d ON d.m_product_id = c.m_product_id
            JOIN pos.pos_m_warehouse AS e ON e.m_warehouse_id = c.m_warehouse_id
            JOIN pos.pos_c_tipodocumento AS f ON a.c_tipodocumento_id = f.c_tipodocumento_id
            JOIN pos.pos_ad_org AS g ON g.ad_org_id_sap = e.m_warehouse_id_sap
            JOIN pos.pos_ad_user AS h on a.ad_user_id = h.ad_user_id
            JOIN pos.pos_ad_user AS x on c.ad_user_id = x.ad_user_id
            JOIN pos.pos_c_bpartner AS i on i.c_bpartner_id = h.c_bpartner_id
            WHERE a.mail_cliente IS NOT NULL 
                AND a.fecha_emision BETWEEN '" . $request->dateStart . "' AND '" . $request->dateEnd . "'
                AND g.ventas not in ('ECOMMERCE')
	            AND d.description not in ('GASTOS DE ENVIO')
            GROUP BY 
                a.fecha_emision,
                to_char(a.creado, 'HH24:MI:SS'),
                a.nro_documento,
                a.estado_docto,
                e.name,
                e.m_warehouse_id_sap,
                d.sku,
                d.description,
                c.valor_unitario,
                a.mail_cliente,
                f.descripcion,
                h.name,
                h.value,
                i.taxid,
                h.name,
                x.name
                ORDER BY a.fecha_emision, to_char(a.creado, 'HH24:MI:SS') ASC;         
        ");
        } elseif (empty($request->dateStart) && empty($request->dateEnd)) {
            $result = DB::connection($request->org)->select("
            SELECT 
                a.fecha_emision AS Fecha_documento,
                to_char(a.creado, 'HH24:MI:SS') AS hora, 
                a.nro_documento AS folio_boleta, 
                a.estado_docto,
                e.name AS tienda,
                TRIM(LEADING '0' FROM e.m_warehouse_id_sap) AS warehouse_id_sap, 
                d.sku AS codigo_articulo, 
                d.description AS descripcion_articulo,
                SUM(c.cantidad) AS unidades_articulo,
                TO_CHAR(CAST(c.valor_unitario AS INTEGER), 'L999G999G999') AS Monto_venta,
                a.mail_cliente AS email_cliente,
                f.descripcion AS tipo_documento,
                h.value AS cod_vendedor,
                i.taxid AS rut_vendedor,
                x.name AS vendedor,
                h.name as cajero
            FROM pos.pos_c_documento AS a
            INNER JOIN pos.pos_c_detalledocumento AS c ON a.c_documento_id = c.c_documento_id
            JOIN pos.pos_m_producto AS d ON d.m_product_id = c.m_product_id
            JOIN pos.pos_m_warehouse AS e ON e.m_warehouse_id = c.m_warehouse_id
            JOIN pos.pos_c_tipodocumento AS f ON a.c_tipodocumento_id = f.c_tipodocumento_id
            JOIN pos.pos_ad_org AS g ON g.ad_org_id_sap = e.m_warehouse_id_sap
            JOIN pos.pos_ad_user AS h on a.ad_user_id = h.ad_user_id
            JOIN pos.pos_ad_user AS x on c.ad_user_id = x.ad_user_id
            JOIN pos.pos_c_bpartner AS i on i.c_bpartner_id = h.c_bpartner_id
            WHERE a.mail_cliente IS NOT NULL 
                AND a.fecha_emision BETWEEN '" . $fechaActual . "' AND '" . $fechaActual . "'
                AND g.banner = '" . $request->banner . "'
                AND g.ventas not in ('ECOMMERCE')
	            AND d.description not in ('GASTOS DE ENVIO')
            GROUP BY 
                a.fecha_emision,
                to_char(a.creado, 'HH24:MI:SS'),
                a.nro_documento,
                a.estado_docto,
                e.name,
                e.m_warehouse_id_sap,
                d.sku,
                d.description,
                c.valor_unitario,
                a.mail_cliente,
                f.descripcion,
                h.name,
                h.value,
                i.taxid,
                h.name,
                x.name
                ORDER BY a.fecha_emision, to_char(a.creado, 'HH24:MI:SS') ASC;          
            ");
        } else {

            $result = DB::connection($request->org)->select("
            SELECT 
                a.fecha_emision AS Fecha_documento,
                to_char(a.creado, 'HH24:MI:SS') AS hora, 
                a.nro_documento AS folio_boleta, 
                a.estado_docto,
                e.name AS tienda,
                TRIM(LEADING '0' FROM e.m_warehouse_id_sap) AS warehouse_id_sap, 
                d.sku AS codigo_articulo, 
                d.description AS descripcion_articulo,
                SUM(c.cantidad) AS unidades_articulo,
                TO_CHAR(CAST(c.valor_unitario AS INTEGER), 'L999G999G999') AS Monto_venta,
                a.mail_cliente AS email_cliente,
                f.descripcion AS tipo_documento,
                h.value AS cod_vendedor,
                i.taxid AS rut_vendedor,
                x.name AS vendedor,
                h.name as cajero
            FROM pos.pos_c_documento AS a
            INNER JOIN pos.pos_c_detalledocumento AS c ON a.c_documento_id = c.c_documento_id
            JOIN pos.pos_m_producto AS d ON d.m_product_id = c.m_product_id
            JOIN pos.pos_m_warehouse AS e ON e.m_warehouse_id = c.m_warehouse_id
            JOIN pos.pos_c_tipodocumento AS f ON a.c_tipodocumento_id = f.c_tipodocumento_id
            JOIN pos.pos_ad_org AS g ON g.ad_org_id_sap = e.m_warehouse_id_sap
            JOIN pos.pos_ad_user AS h on a.ad_user_id = h.ad_user_id
            JOIN pos.pos_ad_user AS x on c.ad_user_id = x.ad_user_id
            JOIN pos.pos_c_bpartner AS i on i.c_bpartner_id = h.c_bpartner_id
            WHERE a.mail_cliente IS NOT NULL 
                AND a.fecha_emision BETWEEN '" . $request->dateStart . "' AND '" . $request->dateEnd . "'
                AND g.banner = '" . $request->banner . "'
                AND g.ventas not in ('ECOMMERCE')
	            AND d.description not in ('GASTOS DE ENVIO')
            GROUP BY 
                a.fecha_emision,
                to_char(a.creado, 'HH24:MI:SS'),
                a.nro_documento,
                a.estado_docto,
                e.name,
                e.m_warehouse_id_sap,
                d.sku,
                d.description,
                c.valor_unitario,
                a.mail_cliente,
                f.descripcion,
                h.name,
                h.value,
                i.taxid,
                h.name,
                x.name
                ORDER BY a.fecha_emision, to_char(a.creado, 'HH24:MI:SS') ASC;
            ");
        }
        return Inertia::render('PosReport/SalesPos', compact('result', 'organizacion', 'banners'));
    }
}
