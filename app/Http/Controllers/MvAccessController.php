<?php

namespace App\Http\Controllers;

use Inertia\Inertia;

use Illuminate\Http\Request;

use App\Models\MvAccess;

class MvAccessController extends Controller
{
    public function __construct()
    {
        //$this->middleware('can:mvaccess.index')->only('index');
        //$this->middleware('can:company.show')->only('show');
        //$this->middleware('can:company.create')->only('create');
        //$this->middleware('can:company.edit')->only('edit');
        //$this->middleware('can:company.destroy')->only('destroy');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $mvAccess = MvAccess::select('id', 'base_url', 'client_id', 'client_secret', 'code', 'expires_token')->paginate(5);

        return Inertia::render('MvAccess/Index', compact('mvAccess'));
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
        $mvAccess = new MvAccess();
        $mvAccess->id = $request->id;
        $mvAccess->base_url = $request->base_url;
        $mvAccess->client_id = $request->client_id;
        $mvAccess->client_secret = $request->client_secret;
        $mvAccess->code = $request->code;
        $mvAccess->expires_token = $request->expires_token;
        $mvAccess->save();

        return redirect()->route('mvaccess.index');
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
        $mvAccess = MvAccess::findorfail($id);

        return Inertia::render('MvAccess/Edit', compact('mvAccess'));
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
        $mvAccess = MvAccess::findOrFail($id);
        $mvAccess->code = $request->code;
        $mvAccess->merchant_id = $request->merchant_id ;
        $mvAccess->token = $request->token ;
        $mvAccess->token_refresh = $request->token_refresh;
        $mvAccess->expires_token = $request->expires_token;
        $mvAccess->save();

        return redirect()->route('mvaccess.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $mvAccess = MvAccess::findOrFail($id);
        $mvAccess->code = null;
        $mvAccess->merchant_id = null;
        $mvAccess->token = null;
        $mvAccess->token_refresh = null;
        $mvAccess->expires_token = null;
        $mvAccess->save();

        return redirect()->route('mvaccess.index');
    }
}
