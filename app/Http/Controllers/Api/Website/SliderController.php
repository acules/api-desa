<?php

namespace App\Http\Controllers\Api\Website;

use App\Models\Slider;
use App\Http\Controllers\Controller;
use App\Http\Resources\PermissionResource;

class SliderController extends Controller
{
    public function index()
    {
        $sliders = Slider::latest()->get();
        return new PermissionResource(true, 'List Data Sliders', $sliders);
    }
}
