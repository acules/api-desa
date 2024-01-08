<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Slider;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\PermissionResource;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;


class SliderController extends Controller
{
    public function index()
    {
        $sliders = Slider::latest()->paginate(5);
        return new PermissionResource(true, 'List Data Sliders', $sliders);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'required|image|mimes:jpeg,jpg,png|max:2000',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $image = $request->file('image');
        $image->storeAs('public/sliders', $image->hashName());

        $slider = Slider::create([
            'image' => 'http://localhost:8000/storage/sliders/'.$image->hashName(),
        ]);

        if ($slider) {
            return new PermissionResource(true, 'Data Slider Berhasil Disimpan!', $slider);
        }
        return new PermissionResource(false, 'Data Slider Gagal Disimpan!', null);
    }

    public function destroy(Slider $slider)
    {
        Storage::disk('local')->delete('public/sliders/' . basename($slider->image));

        if ($slider->delete()) {
            return new PermissionResource(true, 'Data Slider Berhasil Dihapus!', null);
        }
        return new PermissionResource(false, 'Data Slider Gagal Dihapus!', null);
    }
}
