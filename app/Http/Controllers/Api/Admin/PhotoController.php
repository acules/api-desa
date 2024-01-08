<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Photo;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\PermissionResource;
use Illuminate\Support\Facades\Validator;

class PhotoController extends Controller
{
    public function index()
    {
        $photos = Photo::when(request()->search, function ($photos) {
            $photos = $photos->where('caption', 'like', '%' . request()->search . '%');
        })->latest()->paginate(5);

        $photos->appends(['search' => request()->search]);
        return new PermissionResource(true, 'List Data Photos', $photos);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image'    => 'required|mimes:jpeg,jpg,png|max:2000',
            'caption'  => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $image = $request->file('image');
        $image->storeAs('public/photos', $image->hashName());

        $Photo = Photo::create([
            'image' => 'http://localhost:8000/storage/photos/'.$image->hashName(),
            'caption' => $request->caption,
        ]);

        if ($Photo) {
            return new PermissionResource(true, 'Data Photo Berhasil Disimpan!', $Photo);
        }
        return new PermissionResource(false, 'Data Photo Gagal Disimpan!', null);
    }

    public function destroy(Photo $Photo)
    {
        Storage::disk('local')->delete('public/photos/' . basename($Photo->image));

        if ($Photo->delete()) {
            return new PermissionResource(true, 'Data Photo Berhasil Dihapus!', null);
        }
        return new PermissionResource(false, 'Data Photo Gagal Dihapus!', null);
    }
}
