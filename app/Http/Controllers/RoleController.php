<?php

namespace App\Http\Controllers;

use Inertia\Inertia;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Request as Peticion;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

use App\Models\User;
use App\Models\Role;
use App\Models\Permission;

class RoleController extends Controller
{

    public function __construct()
    {
        $this->middleware('can:role.index')->only('index');
        $this->middleware('can:role.show')->only('show');
        $this->middleware('can:role.create')->only('create');
        $this->middleware('can:role.edit')->only('edit');
        $this->middleware('can:role.destroy')->only('destroy');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $roles = Role::query()
            ->select('id', 'name', 'created_at', 'updated_at')
            ->orderBy('id')
            ->when(Peticion::input('search'), function ($query, $search) {
                $query->where('id', 'like', '%' . $search . '%')
                    ->OrWhere('name', 'like', '%' . $search . '%');
            })->paginate(25);

        return Inertia::render('Role/Index', compact('roles'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $dbPermissions = Permission::select('id', 'name')->orderBy('id')->get();
        return Inertia::render('Role/Create', compact('dbPermissions'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $role = new Role;
        $role->name = $request->name;
        $role->guard_name = 'web';

        if ($role->save()) {
            $role->permissions()->sync($request->dbPermission);
        }

        return redirect()->route('role.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $roles = Role::query()
            ->select('id', 'name', 'created_at', 'updated_at')
            ->where('id', $id)
            ->when(Peticion::input('search'), function ($query, $search) {
                $query->where('id', 'like', '%' . $search . '%')
                    ->OrWhere('name', 'like', '%' . $search . '%');
            })->paginate(25);

        return Inertia::render('Role/Index', compact('roles'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $role = Role::findorfail($id);
        $dbPermissions = Permission::select('id', 'name')->orderBy('id')->get();
        $permissionsRol = DB::select("select b.permission_id from roles as a
        join role_has_permissions as b on (a.id = b.role_id) where a.id = $id ");
        return Inertia::render('Role/Edit', compact('role', 'dbPermissions', 'permissionsRol'));
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
        $role = Role::findOrFail($id);
        $role->name = $request->name;
        

        if ($role->save()) {
            $role->permissions()->sync($request->dbPermission);
        }

        return redirect()->route('role.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $role = Role::findOrFail($id);
        $role->delete();

        return redirect()->route('role.index');
    }
}
