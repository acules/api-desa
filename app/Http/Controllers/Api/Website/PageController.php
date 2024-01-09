<?php

namespace App\Http\Controllers\Api\Website;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Http\Resources\PermissionResource;

class PageController extends Controller
{
    public function index()
    {
        $pages = Page::oldest()->get();
        return new PermissionResource(true, 'List Data Pages', $pages);
    }

    public function show($slug)
    {
        $page = Page::where('slug', $slug)->first();

        if ($page) {
            return new PermissionResource(true, 'Detail Data Page', $page);
        }
        return new PermissionResource(false, 'Detail Data Page Tidak Ditemukan!', null);
    }
}
