<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\ApiRepository;
use Illuminate\Http\Request;
use App\Models\SiteInfo;
use App\Models\Media;

class BlogController extends Controller
{
    private $apiRepo;

    /**
     * WebsiteController constructor.
     */
    public function __construct()
    {
        $this->apiRepo = new ApiRepository();
    }

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
        $posts = $comments = $this->apiRepo->getPosts();

        $data  =[
            'page' => 'blog',
            'page_title' => 'Blog',
            'posts' => is_array($posts) ? $posts : []
        ];

        return view('admin.blog.blog', $data);
    }

    /**
     *
     */
    public function new()
    {
        $data  = [
            'page' => 'blog-new',
            'page_title' => 'Nova publicação'
        ];

        return view('admin.blog.new', $data);
    }

    /**
     *
     */
    public function edit($post_id)
    {
        $post = $this->apiRepo->getSinglePost($post_id);
        $data  =[
            'page' => 'blog-edit',
            'page_title' => 'Editar Publicação',
            'post' => $post
        ];

        return view('admin.blog.edit', $data);
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

        if (!isset($request->title) || !isset($request->body)) {
            $response['status'] = 'error';
            $response['message'] = 'preencha todos os campos';
            return response()->json($response);
        }

        $post = $this->apiRepo->newPost($request);

        if ($post) {
            $response['status'] = 'ok';
            $response['message'] = 'sucesso ao criar publicação';
            $response['redirect'] = "/admin/blog";
        }
        else {
            $response['status'] = 'error';
            $response['message'] = 'erro ao tentar criar publicação';
        }

        return response()->json($response);
    }

    /**
     *
     */
    public function update($post_id, Request $request)
    {
        $response = [];

        if (!isset($request->title) || !isset($request->body)) {
            $response['status'] = 'error';
            $response['message'] = 'preencha todos os campos';
            return response()->json($response);
        }

        $post = $this->apiRepo->updatePost($post_id, $request);

        if ($post) {
            $response['status'] = 'ok';
            $response['message'] = 'sucesso ao atualizar publicação';
            $response['redirect'] = "/admin/blog";
        }
        else {
            $response['status'] = 'error';
            $response['message'] = 'erro ao tentar atualizar publicação';
        }

        return response()->json($response);
    }

    /**
     *
     */
    public function delete($post_id)
    {
        $response = [];

        $post = $this->apiRepo->deletePost($post_id);

        if ($post) {
            $response['status'] = 'ok';
            $response['message'] = 'sucesso ao deletar publicação';
            $response['redirect'] = "/admin/blog";
        }
        else {
            $response['status'] = 'error';
            $response['message'] = 'erro ao tentar deletar publicação';
        }

        return response()->json($response);
    }
}
