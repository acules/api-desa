<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\PermissionResource;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function index()
    {
        $users = User::when(request()->search, function($users) {
            $users = $users->where('name', 'like', '%'. request()->search . '%');
        })->with('roles')->latest()->paginate(5);

        $users->appends(['search' => request()->search]);
        
        return new PermissionResource(true, 'List Data Users', $users);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'     => 'required',
            'email'    => 'required|unique:users',
            'password' => 'required|confirmed' 
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        //create user
        $user = User::create([
            'name'      => $request->name,
            'email'     => $request->email,
            'password'  => bcrypt($request->password)
        ]);

        //assign roles to user
        $user->assignRole($request->roles);

        if($user) {
            return new PermissionResource(true, 'Data User Berhasil Disimpan!', $user);
        }
        return new PermissionResource(false, 'Data User Gagal Disimpan!', null);
    }

    public function show($id)
    {
        $user = User::with('roles')->whereId($id)->first();
        
        if($user) {
            return new PermissionResource(true, 'Detail Data User!', $user);
        }
        return new PermissionResource(false, 'Detail Data User Tidak DItemukan!', null);
    }

    public function update(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'name'     => 'required',
            'email'    => 'required|unique:users,email,'.$user->id,
            'password' => 'confirmed'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        if($request->password == "") {
            $user->update([
                'name'      => $request->name,
                'email'     => $request->email,
            ]);

        } else {
            $user->update([
                'name'      => $request->name,
                'email'     => $request->email,
                'password'  => bcrypt($request->password)
            ]);

        }

        //assign roles to user
        $user->syncRoles($request->roles);

        if($user) {
            return new PermissionResource(true, 'Data User Berhasil Diupdate!', $user);
        }
        return new PermissionResource(false, 'Data User Gagal Diupdate!', null);
    }

    public function destroy(User $user)
    {
        if($user->delete()) {
            return new PermissionResource(true, 'Data User Berhasil Dihapus!', null);
        }
        return new PermissionResource(false, 'Data User Gagal Dihapus!', null);
    }

}
