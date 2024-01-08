<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Product;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\PermissionResource;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::when(request()->search, function ($products) {
            $products = $products->where('title', 'like', '%' . request()->search . '%');
        })->latest()->paginate(5);

        $products->appends(['search' => request()->search]);
        return new PermissionResource(true, 'List Data Products', $products);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image'    => 'required|mimes:jpeg,jpg,png|max:2000',
            'title'    => 'required',
            'content'  => 'required',
            'owner'    => 'required',
            'price'    => 'required',
            'address'  => 'required',
            'phone'    => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        //upload image
        $image = $request->file('image');
        $image->storeAs('public/products', $image->hashName());

        //create Product
        $product = Product::create([
            'image'       => 'http://localhost:8000/storage/products/'.$image->hashName(),
            'title' => $request->title,
            'slug'  => Str::slug($request->title, '-'),
            'content' => $request->content,
            'owner' => $request->owner,
            'price' => $request->price,
            'address' => $request->address,
            'phone' => $request->phone,
            'user_id'     => auth()->guard('api')->user()->id,
        ]);

        if ($product) {
            return new PermissionResource(true, 'Data Product Berhasil Disimpan!', $product);
        }
        return new PermissionResource(false, 'Data Product Gagal Disimpan!', null);
    }

    public function show($id)
    {
        $product = Product::whereId($id)->first();

        if ($product) {
            return new PermissionResource(true, 'Detail Data Product!', $product);
        }
        return new PermissionResource(false, 'Detail Data Product Tidak DItemukan!', null);
    }

    public function update(Request $request, Product $product)
    {
        $validator = Validator::make($request->all(), [
            'title'    => 'required',
            'content'  => 'required',
            'owner'    => 'required',
            'price'    => 'required',
            'address'  => 'required',
            'phone'    => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        //check image update
        if ($request->file('image')) {

            //remove old image
            Storage::disk('local')->delete('public/products/' . basename($product->image));

            //upload new image
            $image = $request->file('image');
            $image->storeAs('public/products', $image->hashName());

            //update Product with new image
            $product->update([
                'image' => 'http://localhost:8000/storage/products/'.$image->hashName(),
                'title' => $request->title,
                'slug'  => Str::slug($request->title, '-'),
                'content' => $request->content,
                'owner' => $request->owner,
                'price' => $request->price,
                'address' => $request->address,
                'phone' => $request->phone,
                'user_id'     => auth()->guard('api')->user()->id,
            ]);
        }

        //update Product without image
        $product->update([
            'title' => $request->title,
            'slug'  => Str::slug($request->title, '-'),
            'content' => $request->content,
            'owner' => $request->owner,
            'price' => $request->price,
            'address' => $request->address,
            'phone' => $request->phone,
            'user_id'     => auth()->guard('api')->user()->id,
        ]);

        if ($product) {
            return new PermissionResource(true, 'Data Product Berhasil Diupdate!', $product);
        }
        return new PermissionResource(false, 'Data Product Gagal Diupdate!', null);
    }

    public function destroy(Product $product)
    {
        Storage::disk('local')->delete('public/products/' . basename($product->image));

        if ($product->delete()) {
            return new PermissionResource(true, 'Data Product Berhasil Dihapus!', null);
        }
        return new PermissionResource(false, 'Data Product Gagal Dihapus!', null);
    }

}
