<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class AssurantController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function __construct()
    {
        $this->middleware('can:assurant.index')->only('index');
    }

    public function index()
    {

        $ventas = DB::connection('bels')->select("SELECT
            CASE 
                WHEN a.c_tipodocumento_id IN (101,102) THEN 'FC'
                WHEN a.c_tipodocumento_id IN (104)     THEN 'NC'
                ELSE 'ST'
            END AS tipo,
            a.nro_documento AS nro_boleta, 
            LTRIM(d.c_pos_id_sap, 'C') AS nro_caja,
            e.value AS nro_tienda,
            to_char(a.fecha_emision, 'DD/MM/YYYY') AS fecha, 
            f.taxid AS rut,
            g.sku AS sku,
            b.nro_serie_apple AS nro_serie,
            b.cantidad AS cantidad,
            to_char(round(b.valor_total),'9G999G999') as venta
            FROM pos.pos_c_documento            AS a
            JOIN pos.pos_c_detalledocumento     AS b ON a.c_documento_id = b.c_documento_id
            JOIN pos.pos_c_sesion               AS c ON (a.c_sesion_id = c.c_sesion_id)
            JOIN pos.pos_c_pos                  AS d ON (c.c_pos_id = d.c_pos_id)
            JOIN pos.pos_m_warehouse            AS e ON (d.m_warehouse_id = e.m_warehouse_id)
            LEFT JOIN pos.pos_c_bpartner        AS f ON (a.c_bpartner_id = f.c_bpartner_id)
            JOIN pos.pos_m_producto             AS g ON (b.m_product_id = g.m_product_id)
            WHERE a.c_documento_id IN ( SELECT a.c_documento_id
                                    FROM pos.pos_c_documento        AS a
                                    JOIN pos.pos_c_detalledocumento AS b ON a.c_documento_id = b.c_documento_id
                                    JOIN pos.pos_m_producto         AS c ON b.m_product_id = c.m_product_id
                                    WHERE tipo_material_sap IN ('ZPRI','ZDED'))
            AND a.fecha_emision BETWEEN 'now()' AND 'now()'
            order by 5,2,4");

        return Inertia::render('Assurant/Index', compact('ventas'));
    }

    public function search(Request $request)
    {

        $ventas = DB::connection('bels')->select("SELECT
            CASE 
                WHEN a.c_tipodocumento_id IN (101,102) THEN 'FC'
                WHEN a.c_tipodocumento_id IN (104)     THEN 'NC'
                ELSE 'ST'
            END AS tipo,
            a.nro_documento AS nro_boleta, 
            LTRIM(d.c_pos_id_sap, 'C') AS nro_caja,
            e.value AS nro_tienda,
            to_char(a.fecha_emision, 'DD/MM/YYYY') AS fecha, 
            f.taxid AS rut,
            g.sku AS sku,
            b.nro_serie_apple AS nro_serie,
            b.cantidad AS cantidad,
            to_char(round(b.valor_total),'9G999G999') as venta
            FROM pos.pos_c_documento            AS a
            JOIN pos.pos_c_detalledocumento     AS b ON a.c_documento_id = b.c_documento_id
            JOIN pos.pos_c_sesion               AS c ON (a.c_sesion_id = c.c_sesion_id)
            JOIN pos.pos_c_pos                  AS d ON (c.c_pos_id = d.c_pos_id)
            JOIN pos.pos_m_warehouse            AS e ON (d.m_warehouse_id = e.m_warehouse_id)
            LEFT JOIN pos.pos_c_bpartner        AS f ON (a.c_bpartner_id = f.c_bpartner_id)
            JOIN pos.pos_m_producto             AS g ON (b.m_product_id = g.m_product_id)
            WHERE a.c_documento_id IN ( SELECT a.c_documento_id
                                    FROM pos.pos_c_documento        AS a
                                    JOIN pos.pos_c_detalledocumento AS b ON a.c_documento_id = b.c_documento_id
                                    JOIN pos.pos_m_producto         AS c ON b.m_product_id = c.m_product_id
                                    WHERE tipo_material_sap IN ('ZPRI','ZDED'))
             AND a.fecha_emision BETWEEN '$request->fecha_inicial' AND '$request->fecha_final'
             ORDER BY 5,2,4");

        return Inertia::render('Assurant/Index', compact('ventas'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
