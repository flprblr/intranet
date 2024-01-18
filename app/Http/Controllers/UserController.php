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

class UserController extends Controller
{

    public function __construct()
    {
        $this->middleware('can:user.index')->only('index');
        $this->middleware('can:user.show')->only('show');
        $this->middleware('can:user.create')->only('create');
        $this->middleware('can:user.edit')->only('edit');
        $this->middleware('can:user.destroy')->only('destroy');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::query()
            ->select('users.id', 'users.name', 'users.email', 'users.created_at', 'users.updated_at','m.model_id','r.name as role')
            ->leftjoin('model_has_roles as m','m.model_id','=','users.id')
            ->leftjoin('roles as r','r.id','=','m.role_id')
            ->orderBy('users.id')
            ->when(Peticion::input('search'), function ($query, $search) {
                $query->where('users.name', 'like', '%' . $search . '%')
                    ->OrWhere('users.email', 'like', '%' . $search . '%')
                    ->OrWhere('r.name', 'like', '%' . $search . '%');
            })->paginate(25);

        return Inertia::render('User/Index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $roles = Role::select('id', 'name')->orderBy('id')->get();

        return Inertia::render('User/Create', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $role = Role::where('name', $request->role)->first();
        $user = new User;
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);

        if ($user->save()) {
            $user->assignRole($role);
        }

        return redirect()->route('user.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $users = User::query()
            ->select('id', 'name', 'email', 'created_at', 'updated_at')
            ->where('id', $id)
            ->when(Peticion::input('search'), function ($query, $search) {
                $query->where('name', 'like', '%' . $search . '%')
                    ->OrWhere('email', 'like', '%' . $search . '%');
            })->paginate(5);

        return Inertia::render('User/Index', compact('users'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $users = User::findorfail($id);
        $roles = Role::select('id', 'name')->orderBy('id')->get();
        $rolasignado = DB::select("select distinct c.id, c.name from users as a 
        left join model_has_roles as b on (a.id = b.model_id)   
        left join roles as c on (b.role_id = c.id)  where a.id = $id  ");

        return Inertia::render('User/Edit', compact('users', 'roles', 'rolasignado'));
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
        $role = Role::where('name', $request->role)->first();
        $user = User::findOrFail($id);
        $user->name = $request->name;
        $user->email = $request->email;
        
        if ($request->password != null) {
            $user->password = Hash::make($request->password);
        }
        
        if ($user->save()) {
            $user->syncRoles($role);
        }

        return redirect()->route('user.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('user.index');
    }
}
