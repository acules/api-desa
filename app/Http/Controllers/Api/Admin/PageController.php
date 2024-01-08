<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Page;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\PermissionResource;
use Illuminate\Support\Facades\Validator;


class PageController extends Controller
{
    public function index()
    {
        //get pages
        $pages = Page::when(request()->search, function ($pages) {
            $pages = $pages->where('title', 'like', '%' . request()->search . '%');
        })->latest()->paginate(5);

        $pages->appends(['search' => request()->search]);
        return new PermissionResource(true, 'List Data Pages', $pages);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title'     => 'required',
            'content'   => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $page = Page::create([
            'title'     => $request->title,
            'slug'      => Str::slug($request->title),
            'content'   => $request->content,
            'user_id'   => auth()->guard('api')->user()->id
        ]);

        if ($page) {
            return new PermissionResource(true, 'Data Page Berhasil Disimpan!', $page);
        }
        return new PermissionResource(false, 'Data Page Gagal Disimpan!', null);
    }

    public function show($id)
    {
        $page = Page::whereId($id)->first();

        if ($page) {
            return new PermissionResource(true, 'Detail Data Page!', $page);
        }
        return new PermissionResource(false, 'Detail Data Page Tidak DItemukan!', null);
    }

    public function update(Request $request, Page $page)
    {
        $validator = Validator::make($request->all(), [
            'title'     => 'required',
            'content'   => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $page->update([
            'title'     => $request->title,
            'slug'      => Str::slug($request->title),
            'content'   => $request->content,
            'user_id'   => auth()->guard('api')->user()->id
        ]);

        if ($page) {
            return new PermissionResource(true, 'Data Page Berhasil Diupdate!', $page);
        }
        return new PermissionResource(false, 'Data Page Gagal Diupdate!', null);
    }

    public function destroy(Page $page)
    {
        if ($page->delete()) {
            return new PermissionResource(true, 'Data Page Berhasil Dihapus!', null);
        }
        return new PermissionResource(false, 'Data Page Gagal Dihapus!', null);
    }


}
