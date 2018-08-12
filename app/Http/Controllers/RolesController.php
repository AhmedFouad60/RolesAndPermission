<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;


class RolesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $roles = Role::all();//Get all roles

        return view('roles.index')->with('roles', $roles);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $permissions = Permission::all();//Get all permissions

        return view('roles.create', ['permissions'=>$permissions]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        //Validate name and permissions field
        $this->validate($request, [
                'name'=>'required|unique:roles|max:10',
//                'permissions' =>'required',
            ]
        );

        $name = $request['name'];
        $role = new Role();
        $role->name = $name;

        $permissions = $request['permissions'];

        $role->save();
        //Looping thru selected permissions
//        foreach ($permissions as $permission) {
//            $p = Permission::where('id', '=', $permission)->firstOrFail();
//            //Fetch the newly created role and assign permission
//            $role = Role::where('name', '=', $name)->first();
//            $role->givePermissionTo($p);
//        }

        return redirect()->route('roles.index')
            ->with('flash_message',
                'Role'. $role->name.' added!');
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
        $role = Role::findOrFail($id);
//        $permissions = Permission::all();
        $permissions =[];

        return view('roles.edit', compact('role','permissions'));
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
        $role=Role::findOrFail($id); //get the role with the given $id

        $this->validate($request, [
            'name'=>'required|max:10|unique:roles,name,'.$id,
//            'permissions' =>'required',
        ]);

        $input = $request->except(['permissions']);

        $role->fill($input)->save();

        //Get All permissions

        //Remove all permissions associated with role

        //get the permission from the checkboxes  and find them in the DB and assign permission to the role





        return redirect()->route('roles.index')
            ->with('flash_message',
                'Role'. $role->name.' updated!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $role=Role::findOrFail($id);
        $role->delete();
        return redirect()->route('roles.index')
                ->with('flash_message','Role deleted!');
    }
}
