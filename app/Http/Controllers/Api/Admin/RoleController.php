<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Http\Resources\PermissionResource;
use Illuminate\Support\Facades\Validator;

class RoleController extends Controller
{
    public function index()
    {
        //get roles
        $roles = Role::when(request()->search, function($roles) {
            $roles = $roles->where('name', 'like', '%'. request()->search . '%');
        })->with('permissions')->latest()->paginate(5);

        $roles->appends(['search' => request()->search]);

        return new PermissionResource(true, 'List Data Roles', $roles);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'          => 'required',
            'permissions'   => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        $role = Role::create(['name' => $request->name]);
        $role->givePermissionTo($request->permissions);

        if($role) {
            return new PermissionResource(true, 'Data Role Berhasil Disimpan!', $role);
        }
        return new PermissionResource(false, 'Data Role Gagal Disimpan!', null);
    }

    public function show($id)
    {
        $role = Role::with('permissions')->findOrFail($id);

        if($role) {
            return new PermissionResource(true, 'Detail Data Role!', $role);
        }

        return new PermissionResource(false, 'Detail Data Role Tidak Ditemukan!', null);
    }

    public function update(Request $request, Role $role)
    {
        $validator = Validator::make($request->all(), [
            'name'          => 'required',
            'permissions'   => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        $role->update(['name' => $request->name]);

        $role->syncPermissions($request->permissions);

        if($role) {
            return new PermissionResource(true, 'Data Role Berhasil Diupdate!', $role);
        }
        return new PermissionResource(false, 'Data Role Gagal Diupdate!', null);
    }

    public function destroy($id)
    {
        $role = Role::findOrFail($id);

        if($role->delete()) {
            return new PermissionResource(true, 'Data Role Berhasil Dihapus!', null);
        }

        return new PermissionResource(false, 'Data Role Gagal Dihapus!', null);
    }

    public function all()
    {
        $roles = Role::latest()->get();

        return new PermissionResource(true, 'List Data Roles', $roles);
    }
}
