<?php

namespace App\Http\Controllers;

use Inertia\Inertia;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Request AS Peticion;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

use App\Models\User;
use App\Models\Role;
use App\Models\Permission;

class PermissionController extends Controller
{

    public function __construct() {
        $this->middleware('can:permission.index')->only('index');
        $this->middleware('can:permission.show')->only('show');
        $this->middleware('can:permission.create')->only('create');
        $this->middleware('can:permission.edit')->only('edit');
        $this->middleware('can:permission.destroy')->only('destroy');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $dbPermissions = Permission::query()
                ->select('id', 'name', 'created_at', 'updated_at')
                ->orderBy('id')
                ->when(Peticion::input('search'), function ($query, $search) {
                    $query->where('id', 'like', '%' . $search . '%')
                ->OrWhere('name', 'like', '%' . $search . '%');
                })->paginate(100);
        
        return Inertia::render('Permission/Index', compact('dbPermissions'));

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return Inertia::render('Permission/Create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $permission = new Permission;
        $permission->name = $request->name;
        $permission->guard_name = 'web';
        $permission->save();

        return redirect()->route('permission.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $dbPermissions = Permission::query()
                ->select('id', 'name','created_at', 'updated_at')
                ->where('id', $id)
                ->when(Peticion::input('search'), function ($query, $search) {
                    $query->where('name', 'like', '%' . $search . '%')
                ->OrWhere('id', 'like', '%' . $search . '%');
                })->paginate(5);
                
        return Inertia::render('Permission/Index', compact('dbPermissions'));    
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    { 
        $dbPermissions = Permission::findorfail($id);

        return Inertia::render('Permission/Edit', compact('dbPermissions'));
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
        $permission = Permission::findOrFail($id);
        $permission->name = $request->name;
        $permission->save();

        return redirect()->route('permission.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $permission = Permission::findOrFail($id);
        $permission->delete();

        return redirect()->route('permission.index');
    }
}
