<?php

namespace App\Http\Controllers;

use Inertia\Inertia;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Request AS Peticion;

use App\Models\Company;

class CompanyController extends Controller
{

    public function __construct() {
        $this->middleware('can:company.index')->only('index');
        $this->middleware('can:company.show')->only('show');
        $this->middleware('can:company.create')->only('create');
        $this->middleware('can:company.edit')->only('edit');
        $this->middleware('can:company.destroy')->only('destroy');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $companies = Company::query()
                ->select('id', 'name','rut', 'city', 'commune', 'address', 'activity', 'acteco', 'created_at', 'updated_at')
                ->orderBy('id')
                ->when(Peticion::input('search'), function ($query, $search) {
                    $query->where('id', 'like', '%' . $search . '%')
                ->OrWhere('name', 'like', '%' . $search . '%');
                })->paginate(25);
        
        return Inertia::render('Company/Index', compact('companies'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return Inertia::render('Company/Create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $company = new Company;
        $company->name = $request->name;
        $company->rut = $request->rut;
        $company->city = $request->city;
        $company->commune = $request->commune;
        $company->address = $request->address;
        $company->activity = $request->activity;
        $company->acteco = $request->acteco;
        $company->sovos_user = $request->sovos_user;
        $company->sovos_password = $request->sovos_password;
        $company->save();

        return redirect()->route('company.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $companies = Company::query()
            ->select('id', 'name','rut', 'city', 'commune', 'address', 'activity', 'acteco', 'created_at', 'updated_at')
            ->where('id', $id)
            ->when(Peticion::input('search'), function ($query, $search) {
                $query->where('name', 'like', '%' . $search . '%')
            ->OrWhere('id', 'like', '%' . $search . '%');
            })->paginate(5);
                
        return Inertia::render('Company/Index', compact('companies'));   
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $company = Company::findorfail($id);

        return Inertia::render('Company/Edit', compact('company'));
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
        $company = Company::findOrFail($id);
        $company->name = $request->name;
        $company->rut = $request->rut;
        $company->city = $request->city;
        $company->commune = $request->commune;
        $company->address = $request->address;
        $company->activity = $request->activity;
        $company->acteco = $request->acteco;
        $company->sovos_user = $request->sovos_user;
        $company->sovos_password = $request->sovos_password;
        $company->save();

        return redirect()->route('company.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $company = Company::findOrFail($id);
        $company->delete();

        return redirect()->route('company.index');
    }
}
