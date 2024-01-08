<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\PermissionResource;
use Illuminate\Support\Facades\Validator;;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::when(request()->search, function ($categories) {
            $categories = $categories->where('name', 'like', '%' . request()->search . '%');
        })->latest()->paginate(5);

        //append query string to pagination links
        $categories->appends(['search' => request()->search]);
        return new PermissionResource(true, 'List Data Categories', $categories);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'     => 'required|unique:categories',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        $category = Category::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name, '-'),
        ]);

        if ($category) {
            return new PermissionResource(true, 'Data Category Berhasil Disimpan!', $category);
        }
        return new PermissionResource(false, 'Data Category Gagal Disimpan!', null);
    }

    public function show($id)
    {
        $category = Category::whereId($id)->first();

        if ($category) {
            return new PermissionResource(true, 'Detail Data Category!', $category);
        }
        return new PermissionResource(false, 'Detail Data Category Tidak DItemukan!', null);
    }

    public function update(Request $request, Category $category)
    {
        $validator = Validator::make($request->all(), [
            'name'     => 'required|unique:categories,name,' . $category->id,
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $category->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name, '-'),
        ]);

        if ($category) {
            return new PermissionResource(true, 'Data Category Berhasil Diupdate!', $category);
        }
        return new PermissionResource(false, 'Data Category Gagal Diupdate!', null);
    }

    public function destroy(Category $category)
    {
        if ($category->delete()) {
            return new PermissionResource(true, 'Data Category Berhasil Dihapus!', null);
        }

        return new PermissionResource(false, 'Data Category Gagal Dihapus!', null);
    }

    public function all()
    {
        $categories = Category::latest()->get();
        return new PermissionResource(true, 'List Data Categories', $categories);
    }

}
