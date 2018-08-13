<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class permissionsController extends Controller
{
    public function __construct() {
        $this->middleware(['auth', 'isAdmin']); //isAdmin middleware lets only users with a //specific permission permission to access these resources
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $permission=Permission::all();
        return view('permissions.index')->with('permissions',$permission);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $roles=Role::all();
        return view('permissions.create')->with('roles',$roles);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request,[
           'name'=>'required|max:40'
        ]);

        $name=$request['name'];
        $permission=new Permission();
        $permission->name=$name;

        $permission->save();

        $roles=$request['roles'];

        if(!empty($request['roles'])){ // if one or more role is selected
            foreach ($roles as $role) {
                $r=Role::where('id','=',$role)->firstorFail();
                $permission=Permission::where('name','=',$name)->first();
                $r->givePermissionTo($permission);

            }

        }


        return redirect()->route('permissions.index')
            ->with('flash_message',"permission".$permission->name."added!");





    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return redirect('permissions');

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $permission=Permission::findOrFail($id);
        return view('permissions.edit')->with('permission',$permission);

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
        $this->validate($request, [
            'name'=>'required|max:40',
        ]);
        $input = $request->all();
        $permission->fill($input)->save();

        return redirect()->route('permissions.index')
            ->with('flash_message',
                'Permission'. $permission->name.' updated!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //make it impossible to delete this permission
        $permission=Permission::findOrFail($id);
        if ($permission->name =="Administer roles & permissions"){
            return redirect()->route('permissions.index')
                        ->with("flash_message",
                            "can't delete this Permission");
        }

        $permission->delete();
        return redirect()->route('permissions.index')
            ->with('flash_message',
                'Permission deleted!');



    }
}
