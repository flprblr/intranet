<?php

namespace App\Http\Controllers;

use Inertia\Inertia;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Request as Peticion;
use Illuminate\Support\Facades\DB;

use App\Models\Company;
use App\Models\Ecommerce;

class EcommerceController extends Controller
{

    public function __construct() {
        $this->middleware('can:ecommerce.index')->only('index');
        $this->middleware('can:ecommerce.show')->only('show');
        $this->middleware('can:ecommerce.create')->only('create');
        $this->middleware('can:ecommerce.edit')->only('edit');
        $this->middleware('can:ecommerce.destroy')->only('destroy');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $ecommerces = Ecommerce::query()
        ->select('ecommerces.prefix','ecommerces.url', 'ecommerces.api_key', 'ecommerces.api_secret', 'ecommerces.logo', 'ecommerces.VKORG', 'ecommerces.WERKS', 'ecommerces.LGORT', 'ecommerces.AUART', 'ecommerces.FKART', 'ecommerces.id', 'c.name', 'ecommerces.created_at', 'ecommerces.updated_at')
        ->join('companies as c','c.id','=','ecommerces.company_id')
        ->orderBy('ecommerces.id')
        ->when(Peticion::input('search'), function ($query, $search) {
            $query->where('ecommerces.url', 'like', '%' . $search . '%');
        })->paginate(25);

        return Inertia::render('Ecommerce/Index', compact('ecommerces'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $companies = Company::select('id', 'name')->orderBy('id')->get();

        return Inertia::render('Ecommerce/Create', compact('companies'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $ecommerce = new Ecommerce;
        $ecommerce->prefix = $request->prefix;
        $ecommerce->url = $request->url;
        $ecommerce->api_key = $request->api_key;
        $ecommerce->api_secret = $request->api_secret;
        $ecommerce->logo = $request->logo;
        $ecommerce->vkorg = $request->vkorg;
        $ecommerce->werks = $request->werks;
        $ecommerce->lgort = $request->lgort;
        $ecommerce->auart = $request->auart;
        $ecommerce->fkart = $request->fkart;
        $ecommerce->company_id = $request->company_id;
        $ecommerce->save();

        return redirect()->route('ecommerce.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $ecommerces = Ecommerce::query()
        ->select('ecommerces.url', 'ecommerces.api_key', 'ecommerces.api_secret', 'ecommerces.logo', 'ecommerces.VKORG', 'ecommerces.WERKS', 'ecommerces.LGORT', 'ecommerces.AUART', 'ecommerces.FKART', 'ecommerces.id', 'c.name', 'ecommerces.created_at', 'ecommerces.updated_at')
        ->join('companies as c','c.id','=','ecommerces.company_id')
        ->orderBy('ecommerces.id')
        ->where('ecommerces.id', $id)
        ->when(Peticion::input('search'), function ($query, $search) {
            $query->where('ecommerces.url', 'like', '%' . $search . '%');
        })->paginate(25);

        return Inertia::render('Ecommerce/Index', compact('ecommerces'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $ecommerce = Ecommerce::findorfail($id);
        $asignedCompany = Company::select('id', 'name')->where('id', $ecommerce->company_id)->orderBy('id')->get();
        $companies = Company::select('id', 'name')->orderBy('id')->get();

        return Inertia::render('Ecommerce/Edit', compact('ecommerce', 'asignedCompany', 'companies'));
        
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
        $ecommerce = Ecommerce::findorfail($id);
        $ecommerce->prefix = $request->prefix;
        $ecommerce->url = $request->url;
        $ecommerce->api_key = $request->api_key;
        $ecommerce->api_secret = $request->api_secret;
        $ecommerce->logo = $request->logo;
        $ecommerce->vkorg = $request->vkorg;
        $ecommerce->werks = $request->werks;
        $ecommerce->lgort = $request->lgort;
        $ecommerce->auart = $request->auart;
        $ecommerce->fkart = $request->fkart;
        $ecommerce->company_id = $request->company_id;
        $ecommerce->save();

        return redirect()->route('ecommerce.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $ecommerce = Ecommerce::findOrFail($id);
        $ecommerce->delete();

        return redirect()->route('ecommerce.index');
    }
}
