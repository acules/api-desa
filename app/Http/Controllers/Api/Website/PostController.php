<?php

namespace App\Http\Controllers\Api\Website;

use App\Models\Post;
use App\Http\Controllers\Controller;
use App\Http\Resources\PermissionResource;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::with('user', 'category')->latest()->paginate(10);

        return new PermissionResource(true, 'List Data Posts', $posts);
    }

    public function show($slug)
    {
        $post = Post::with('user', 'category')->where('slug', $slug)->first();

        if ($post) {
            return new PermissionResource(true, 'Detail Data Post', $post);
        }
        return new PermissionResource(false, 'Detail Data Post Tidak Ditemukan!', null);
    }

    public function homePage()
    {
        $posts = Post::with('user', 'category')->latest()->take(6)->get();

        return new PermissionResource(true, 'List Data Post HomePage', $posts);
    }

}

