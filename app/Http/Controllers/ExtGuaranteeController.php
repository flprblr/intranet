<?php

namespace App\Http\Controllers;

use App\Models\ext_guarantee;
use App\Models\ext_customer;

use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Request as Peticion;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;


use App\Mail\GarantiaExtendida;
use Illuminate\Support\Facades\Mail;

class ExtGuaranteeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function __construct()
    {
        $this->middleware('can:extguarantee.index')->only('index');
        $this->middleware('can:extguarantee.show')->only('show');
        $this->middleware('can:extguarantee.create')->only('create');
        $this->middleware('can:extguarantee.edit')->only('edit');
        $this->middleware('can:extguarantee.destroy')->only('destroy');
    }

    public function sendEmail($id)
    {

        $ext_guarantee = ext_guarantee::where('id', $id)->first();
        if ($ext_guarantee) {
            $ext_customer = ext_customer::where('id', $ext_guarantee->id_ext_customer)->first();
        }
        Mail::to($ext_customer->email)->send(new GarantiaExtendida($ext_guarantee, $ext_customer));

        return redirect()->back();
    }

    public function index()
    {
        $extguarantees = ext_guarantee::query()
            ->select(
                'ext_customers.rut',
                'ext_customers.full_name',
                'ext_customers.phone',
                'ext_customers.email',
                'ext_guarantees.id',
                'ext_guarantees.xblnr',
                'ext_guarantees.sold_by',
                'ext_guarantees.sellername',
                'ext_guarantees.sold_date',
                'ext_guarantees.matnr',
                'ext_guarantees.type',
                'ext_guarantees.description',
                'ext_guarantees.serial',
                'ext_guarantees.xblnr_gext',
                'ext_guarantees.matnr_gext',
                'ext_guarantees.description_gext',
                'ext_guarantees.valor',
                'ext_guarantees.valid_from',
                'ext_guarantees.valid_to',
                'ext_guarantees.comment',
                'ext_guarantees.active',
                'ext_guarantees.created_by',
                'ext_guarantees.created_at',
                'ext_guarantees.updated_by',
                'ext_guarantees.updated_at',
            )
            ->join('ext_customers', 'ext_customers.id', '=', 'ext_guarantees.id_ext_customer')
            ->orderby('ext_guarantees.id', 'desc')
            ->when(Peticion::input('search'), function ($query, $search) {
                $query->where('ext_guarantees.id', 'like', '%' . $search . '%')
                    ->OrWhere('ext_customers.rut', 'like', '%' . $search . '%')
                    ->OrWhere('ext_customers.xblnr', 'like', '%' . $search . '%')
                    ->OrWhere('ext_customers.matnr', 'like', '%' . $search . '%');
            })->paginate(5);

        return Inertia::render('ExtGuarantee/Index', compact('extguarantees'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $extguarantees = ext_guarantee::select('id')->orderBy('id')->get();


        return Inertia::render('ExtGuarantee/Create', compact('extguarantees'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        try {
            $ExtGuaranteeController = new ExtGuaranteeController();

            $extcustomers = ext_customer::where('rut', $request->rut)->first();
            if (is_null($extcustomers)) {
                $extcustomers = new ext_customer();
                $extcustomers->rut       =  $request->rut;
                $extcustomers->full_name =  $request->full_name;
                $extcustomers->phone     =  $request->phone;
                $extcustomers->email     =  $request->email;
                $extcustomers->save();
            }

            $guara = ext_guarantee::where('xblnr', $request->xblnr)->first();
            if (!is_null($guara)) {
                if ($guara->matnr === $request->matnr) {
                    return redirect()->back()->withErrors(['Duplicado' => 'El Material Selecccionado ' . $request->matnr . ' ya contiene una Garantia Extendida Asosiada']);
                }
            }
            $guaraGext = ext_guarantee::where('xblnr_gext', $request->xblnr_gext)->first();
            if (!is_null($guaraGext)) {
                if ($guaraGext->matnr_gext === $request->matnr_gext) {
                    return redirect()->back()->withErrors(['Duplicado' => 'La Garantia Selecccionada ' . $request->matnr_gext . ' Ya fue asociada']);
                }
            }


            $extguarantees = new ext_guarantee();
            $extguarantees->id_ext_customer     = $extcustomers->id;

            $extguarantees->xblnr               = $request->xblnr;
            $extguarantees->sold_by             = $request->sold_by;
            $extguarantees->sellername          = $request->sellername;
            $extguarantees->sold_date           = $request->fecha_emision;
            $extguarantees->type                = $request->tipo_material_sap;
            $extguarantees->matnr               = $request->matnr;
            $extguarantees->description         = $request->description;
            $extguarantees->serial              = $request->serial;

            //GEXT


            $extguarantees->xblnr_gext          = $request->xblnr_gext;


            $extguarantees->matnr_gext          = $request->matnr_gext;
            $extguarantees->description_gext    = $request->description_gext;
            $extguarantees->valor               = $request->valor_gext;



            $extguarantees->comment             = $request->comment;

            $extguarantees->valid_from          = $request->valid_from;
            $extguarantees->valid_to            = $request->valid_to;

            $extguarantees->created_by          = auth()->user()->name;
            $extguarantees->created_at          = Carbon::now();

            if ($extguarantees->save()) {
                $ExtGuaranteeController->sendEmail($extguarantees->id);
            }

            return redirect()->route('extguarantee.index');
        } catch (QueryException $e) {
            $errorCode = $e->errorInfo[1];
            if ($errorCode == 1062) {
                return redirect()->back()->withErrors(['Duplicado' => 'Duplicado']);
            } elseif ($errorCode == 1452) {
                return redirect()->back()->withErrors(['foreign_key' => 'Error de clave foránea.']);
            } else {
                return redirect()->back()->withErrors($e->getMessage());
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ext_guarantee  $ext_guarantee
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {


        $extguarantees = ext_guarantee::query()
            ->select(
                'ext_guarantees.id',
                'ext_guarantees.id_ext_customer',
                'ext_guarantees.description',
                'ext_guarantees.xblnr',
                'ext_guarantees.sold_by',
                'ext_guarantees.sellername',
                'ext_guarantees.sold_date',
                'ext_guarantees.matnr',
                'ext_guarantees.type',
                'ext_guarantees.description',
                'ext_guarantees.serial',

                // GEXT
                'ext_guarantees.xblnr_gext',
                'ext_guarantees.matnr_gext',
                'ext_guarantees.description_gext',
                'ext_guarantees.valor',

                'ext_guarantees.active',
                'ext_guarantees.comment',
                'ext_guarantees.valid_from',
                'ext_guarantees.valid_to',
                'ext_guarantees.created_by',
                'ext_guarantees.updated_by',
                'ext_guarantees.created_at',
                'ext_guarantees.updated_at',
                'ext_customers.rut',
                'ext_customers.full_name',
                'ext_customers.phone',
                'ext_customers.email',
            )
            ->join('ext_customers', 'ext_customers.id', '=', 'ext_guarantees.id_ext_customer')
            ->where('ext_guarantees.id',  '=', $id)

            ->get();

        return Inertia::render('ExtGuarantee/Show', compact('extguarantees'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ext_guarantee  $ext_guarantee
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

        $extguarantees = ext_guarantee::query()
            ->select(
                'ext_guarantees.id',
                'ext_guarantees.id_ext_customer',
                'ext_guarantees.description',
                'ext_guarantees.xblnr',
                'ext_guarantees.sold_by',
                'ext_guarantees.sellername',
                'ext_guarantees.sold_date',
                'ext_guarantees.matnr',
                'ext_guarantees.type',
                'ext_guarantees.description',
                'ext_guarantees.serial',

                // GEXT
                'ext_guarantees.xblnr_gext',
                'ext_guarantees.matnr_gext',
                'ext_guarantees.description_gext',
                'ext_guarantees.valor',

                'ext_guarantees.active',
                'ext_guarantees.comment',
                'ext_guarantees.valid_from',
                'ext_guarantees.valid_to',
                'ext_guarantees.created_by',
                'ext_guarantees.updated_by',
                'ext_guarantees.created_at',
                'ext_guarantees.updated_at',
                'ext_customers.rut',
                'ext_customers.full_name',
                'ext_customers.phone',
                'ext_customers.email',
            )
            ->join('ext_customers', 'ext_customers.id', '=', 'ext_guarantees.id_ext_customer')
            ->where('ext_guarantees.id',  '=', $id)
            ->get();

        return Inertia::render('ExtGuarantee/Edit', compact('extguarantees'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ext_guarantee  $ext_guarantee
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            $ExtGuaranteeController = new ExtGuaranteeController();

            $extcustomers = ext_customer::where('rut', $request->rut)->first();
            if (is_null($extcustomers)) {
                $extcustomers = new ext_customer();
                $extcustomers->rut       =  $request->rut;
                $extcustomers->full_name =  $request->full_name;
                $extcustomers->phone     =  $request->phone;
                $extcustomers->email     =  $request->email;
                $extcustomers->save();
            }

            $extguarantees = ext_guarantee::findOrFail($id);

            $guara = ext_guarantee::where('xblnr', $request->xblnr)->first();
            if (!is_null($guara)) {
                if ($guara->matnr === $request->matnr) {
                    return redirect()->back()->withErrors(['Duplicado' => 'El Material Selecccionado ' . $request->matnr . ' ya contiene una Garantia Extendida Asosiada']);
                }
            }
            $guaraGext = ext_guarantee::where('xblnr_gext', $request->xblnr_gext)->first();
            if (!is_null($guaraGext)) {
                if ($guaraGext->matnr_gext === $request->matnr_gext) {
                    return redirect()->back()->withErrors(['Duplicado' => 'La Garantia Selecccionada ' . $request->matnr_gext . ' Ya fue asociada']);
                }
            }


            $extguarantees->id_ext_customer     = $extcustomers->id;
            $extguarantees->xblnr               = $request->xblnr;
            $extguarantees->sold_by             = $request->sold_by;
            $extguarantees->sellername          = $request->sellername;
            $extguarantees->sold_date           = $request->fecha_emision;
            $extguarantees->type                = $request->tipo_material_sap;
            $extguarantees->matnr               = $request->matnr;
            $extguarantees->description         = $request->description;
            $extguarantees->serial              = $request->serial;

            //GEXT
            $extguarantees->xblnr_gext          = $request->xblnr_gext;
            $extguarantees->matnr_gext          = $request->matnr_gext;
            $extguarantees->description_gext    = $request->description_gext;
            $extguarantees->valor               = $request->valor_gext;



            $extguarantees->comment             = $request->comment;

            $extguarantees->valid_from          = $request->valid_from;
            $extguarantees->valid_to            = $request->valid_to;

            $extguarantees->updated_by          = auth()->user()->name;
            $extguarantees->updated_at          = Carbon::now();

            $extguarantees->save();
            return redirect()->route('extguarantee.index');
        } catch (QueryException $e) {
            $errorCode = $e->errorInfo[1];
            if ($errorCode == 1062) {
                return redirect()->back()->withErrors(['Duplicado' => 'Duplicado']);
            } elseif ($errorCode == 1452) {
                return redirect()->back()->withErrors(['foreign_key' => 'Error de clave foránea.']);
            } else {
                return redirect()->back()->withErrors($e->getMessage());
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ext_guarantee  $ext_guarantee
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $ext_guarantee = ext_guarantee::findOrFail($id);
        $ext_guarantee->delete();

        return redirect()->route('extguarantee.index');
    }

    public function getInvoiceData($nro_documento)
    {
        $invoices = DB::connection('bels')->select("SELECT a.nro_documento,a.fecha_emision,c.sku,c.description, b.nro_serie_apple,c.tipo_material_sap,round(b.valor_total) as valor_total
        from pos.pos_c_documento  a 
        join pos.pos_c_detalledocumento b on (a.c_documento_id = b.c_documento_id)
        join pos.pos_m_producto c on (b.m_product_id = c.m_product_id)
        where a.nro_documento = $nro_documento and a.estado_docto not in ('A','E') and a.c_tipodocumento_id in (101,102)");

        return response()->json($invoices);
    }
    public function getInvoiceDataGext($nro_documento)
    {
        $invoices = DB::connection('bels')->select("SELECT a.nro_documento,a.fecha_emision,c.sku,c.description, b.nro_serie_apple,c.tipo_material_sap,round(b.valor_total) as valor_total
        from pos.pos_c_documento  a 
        join pos.pos_c_detalledocumento b on (a.c_documento_id = b.c_documento_id)
        join pos.pos_m_producto c on (b.m_product_id = c.m_product_id)
        where a.nro_documento = $nro_documento and a.estado_docto not in ('A','E') and a.c_tipodocumento_id in (101,102)");

        return response()->json($invoices);
    }

    public function getSellerName($sellerCode)
    {
        if ($sellerCode !== '' && $sellerCode !== null  && $sellerCode !== "null") {
            $sellers = DB::connection('bels')->select("SELECT name from pos.pos_ad_user where value = '$sellerCode'");
            return response()->json($sellers);
        } else {
            return response()->json(['name' => '']);
        }
    }
}
