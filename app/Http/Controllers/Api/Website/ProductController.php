<?php

namespace App\Http\Controllers\Api\Website;

use App\Models\Product;
use App\Http\Controllers\Controller;
use App\Http\Resources\PermissionResource;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::latest()->paginate(9);

        return new PermissionResource(true, 'List Data Products', $products);
    }

    public function show($slug)
    {
        $product = Product::where('slug', $slug)->first();

        if ($product) {
            return new PermissionResource(true, 'Detail Data Product', $product);
        }
        return new PermissionResource(false, 'Detail Data Product Tidak Ditemukan!', null);
    }

    public function homePage()
    {
        $products = Product::latest()->take(6)->get();
        return new PermissionResource(true, 'List Data Products HomePage', $products);
    }
}
