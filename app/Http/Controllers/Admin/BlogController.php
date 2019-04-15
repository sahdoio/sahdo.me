<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SiteInfo;
use App\Models\Media;

class BlogController extends Controller
{
    /*
    ###########################
    # View Area
    ###########################
    */

    /**
     * 
     */
    public function blog()
    {
        $data  =[
            'page' => 'blog',
            'page_title' => 'Blog'
        ];

        return view('admin.blog.blog', $data);
    }

    /**
     *
     */
    public function new()
    {
        $data  = [
            'page' => 'banners-new',
            'page_title' => 'Novo Banner'
        ];

        return view('admin.banners.new', $data);
    }

    /**
     *
     */
    public function edit($id)
    {
        $data  =[
            'banner' => Media::find($id),
            'page' => 'banners-edit',
            'page_title' => 'Editar Banner'
        ];

        return view('admin.banners.edit', $data);
    }

    /*
    ###########################
    # Ajax Area
    ###########################
    */

    /**
     *
     */
    public function create(Request $request)
    {
        $response = [];

        if (!isset($request->title) || !isset($request->subtitle) || !isset($request->image)) {
            $response['status'] = 'error';
            $response['message'] = 'preencha todos os campos';
            return response()->json($response);
        }

        $name = 'banner_' . time() . '.png';
        $path = $request->image->storeAs('images', $name);
        if ($path) {
            $path = 'storage/' . $path;
        }
        else {
            $response['status'] = 'error';
            $response['message'] = 'erro ao tentar criar banner';
            return response()->json($response);
        }
        
        $banner = Media::create([
            'title' => $request->title,
            'subtitle' => $request->subtitle,
            'url' => $path,
            'category_id' => 2
        ]);

        if ($banner) {
            $response = $banner->toArray();
            $response['status'] = 'ok';
            $response['message'] = 'sucesso ao criar banner';
            $response['redirect'] = "/admin/banners";
        }
        else {
            $response['status'] = 'error';
            $response['message'] = 'erro ao tentar criar banner';
        }

        return response()->json($response);
    }

    /**
     *
     */
    public function update(Request $request, $id)
    {
        $banner = Media::find($id);

        if (isset($request->title))
            $banner->title = $request->title;

        if (isset($request->subtitle))
            $banner->subtitle = $request->subtitle;

        if (isset($request->image)) {
            $name = 'banner_' . time() . '.png';
            $path = $request->image->storeAs('images', $name);
            if ($path) {
                $path = 'storage/' . $path;
                $banner->url = $path;
            }
            else {
                $response['status'] = 'error';
                $response['message'] = 'falha ao fazer upload da imagem';
                return response()->json($response);
            }
        }

        $banner->save();

        $response = $banner->toArray();
        $response['status'] = 'ok';
        $response['message'] = "success";
        $response['redirect'] = route('admin.banners');

        return response()->json($response);
    }

    /**
     *
     */
    public function delete($id)
    {
        $media = Media::find($id);

        if ($media->delete())
            return response()->json([
                'status' => 'ok',
                'message' => 'sucesso ao deletar banner',
                'redirect' => route('admin.banners')
            ]);
        else
            return response()->json([
                'status' => 'error',
                'message' => 'erro ao deletar banner'
            ]);
    }
}
