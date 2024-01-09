<?php

namespace App\Http\Controllers\Api\Website;

use App\Models\Photo;
use App\Http\Controllers\Controller;
use App\Http\Resources\PermissionResource;

class PhotoController extends Controller
{
    public function index()
    {
        $photos = Photo::latest()->paginate(9);
        return new PermissionResource(true, 'List Data Photos', $photos);
    }
}
