<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Aparatur;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\PermissionResource;
use Illuminate\Support\Facades\Validator;

class AparaturController extends Controller
{
    public function index()
    {
        $aparaturs = Aparatur::when(request()->search, function ($aparaturs) {
            $aparaturs = $aparaturs->where('name', 'like', '%' . request()->search . '%');
        })->latest()->paginate(5);

        $aparaturs->appends(['search' => request()->search]);
        return new PermissionResource(true, 'List Data Aparaturs', $aparaturs);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image'    => 'required|mimes:jpeg,jpg,png|max:2000',
            'name'     => 'required',
            'role'     => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        //upload image
        $image = $request->file('image');
        $image->storeAs('public/aparaturs', $image->hashName());

        $aparatur = Aparatur::create([
            'image' => 'http://localhost:8000/storage/aparaturs/'.$image->hashName(),
            'name'      => $request->name,
            'role'      => $request->role,
        ]);

        if ($aparatur) {
            return new PermissionResource(true, 'Data Aparatur Berhasil Disimpan!', $aparatur);
        }
        return new PermissionResource(false, 'Data Aparatur Gagal Disimpan!', null);
    }

    public function show($id)
    {
        $aparatur = Aparatur::whereId($id)->first();

        if ($aparatur) {
            return new PermissionResource(true, 'Detail Data Aparatur!', $aparatur);
        }
        return new PermissionResource(false, 'Detail Data Aparatur Tidak Ditemukan!', null);
    }

    public function update(Request $request, Aparatur $aparatur)
    {
        $validator = Validator::make($request->all(), [
            'name'     => 'required',
            'role'     => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        //check image update
        if ($request->file('image')) {

            //remove old image
            Storage::disk('local')->delete('public/aparaturs/' . basename($aparatur->image));

            $image = $request->file('image');
            $image->storeAs('public/aparaturs', $image->hashName());

            $aparatur->update([
                'image' => 'http://localhost:8000/storage/aparaturs/'.$image->hashName(),
                'name'  => $request->name,
                'role'  => $request->role,
            ]);
        }

        //update aparatur without image
        $aparatur->update([
            'name' => $request->name,
            'role' => $request->role,
        ]);

        if ($aparatur) {
            return new PermissionResource(true, 'Data Aparatur Berhasil Diupdate!', $aparatur);
        }
        return new PermissionResource(false, 'Data Aparatur Gagal Diupdate!', null);
    }

    public function destroy(Aparatur $aparatur)
    {
        Storage::disk('local')->delete('public/aparaturs/' . basename($aparatur->image));

        if ($aparatur->delete()) {
            return new PermissionResource(true, 'Data Aparatur Berhasil Dihapus!', null);
        }
        return new PermissionResource(false, 'Data Aparatur Gagal Dihapus!', null);
    }
}
