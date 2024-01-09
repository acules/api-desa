<?php

namespace App\Http\Controllers\Api\Website;

use App\Models\Aparatur;
use App\Http\Controllers\Controller;
use App\Http\Resources\PermissionResource;

class AparaturController extends Controller
{
    public function index()
    {
        $aparaturs = Aparatur::oldest()->get();
        return new PermissionResource(true, 'List Data Aparaturs', $aparaturs);
    }
}
