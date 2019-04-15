<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Team;
use App\Models\Media;
use App\Models\MediaCategory;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UsersController extends Controller
{
    /*
    #################################
    # Views
    #################################
    */

    /**
     *
     */
    public function users()
    {
        return view('admin.users.users', [
            'page' => 'users',
            'page_title' => 'Usuários'
        ]);
    }

    /**
     *
     */
    public function new()
    {
        return view('admin.users.new', [
            'page' => 'client-new',
            'page_title' => 'Novo Usuário'
        ]);
    }


    /**
     *
     */
    public function edit($id)
    {
        $data = [
            'page' => 'user-edit',
            'page_title' => 'Editar Usuário',
            'user' => User::find($id)
        ];

        return view('admin.users.edit', $data);
    }

    /*
    #################################
    # Ajax
    #################################
    */

    /**
     *
     */
    public function table(Request $request)
    {
        $start = (int)$request->start;
        $length = ($request->length !== -1) ? (int)$request->length : 50;
        $search = (isset($request->search['value']) && strlen($request->search['value']) > 0) ? $request->search['value'] : false;

        $response = [];
        $response['draw'] = 0;
        $response['data'] = [];

        if ($search) {
            $result = User::orderBy('created_at', 'desc')
                ->where('name', 'like', '%' . $search . '%')
                ->orWhere('email', 'like', '%' . $search . '%')
                ->skip($start)
                ->take($length)
                ->get();

            $response['recordsTotal'] = count($result);
            $response['recordsFiltered'] = count($result);
        }
        else {
            $result = User::orderBy('created_at', 'desc')
                ->skip($start)
                ->take($length)
                ->get();

            $response['recordsTotal'] = Team::count();
            $response['recordsFiltered'] = Team::count();
        }

        $i = 0;
        foreach ($result as $res) {
            $actions = '
                <div>
                    <div class="actions text-center">
                        <a href="' . route('admin.users.edit', $res->id) . '" class="btn btn-round btn-warning btn-icon btn-sm edit">
                            <i class="fa fa-edit"></i>                                        
                        </a>
                        <a href="' . route('admin.users.delete', $res->id) . '" class="btn btn-round btn-danger btn-icon btn-sm remove">
                            <i class="fa fa-minus"></i>                                        
                        </a>
                    </div>
                </td>
            ';

            $level = '';
            if (isset($res->level)) {
                switch ($res->level) {
                    case User::ADMIN:
                        $level = 'Administrador';
                        break;
                    case User::EDITOR:
                        $level = 'Editor';
                        break;
                }
            }

            $data = (object) [
                ++$i,
                isset($res->name) ? $res->name : '',
                isset($res->lastname) ? $res->lastname : '',
                isset($res->email) ? $res->email : '',
                isset($res->about) ? $res->about : '',
                $level,
                $actions
            ];

            $response['data'][] = $data;
        }
        return response()->json($response);
    }

    /**
     *
     */
    public function create(Request $request)
    {
        $response = [];

        if (empty($request->name)) {
            $response['status'] = 'error';
            $response['message'] = 'campo nome obrigatório';
            return response()->json($response);
        }

        if (empty($request->lastname)) {
            $response['status'] = 'error';
            $response['message'] = 'campo sobrenome obrigatório';
            return response()->json($response);
        }

        if (empty($request->email)) {
            $response['status'] = 'error';
            $response['message'] = 'campo email obrigatório';
            return response()->json($response);
        }

        if (empty($request->password)) {
            $response['status'] = 'error';
            $response['message'] = 'campo senha obrigatório';
            return response()->json($response);
        }

        if (empty($request->level)) {
            $response['status'] = 'error';
            $response['message'] = 'campo nível obrigatório';
            return response()->json($response);
        }

        if (empty($request->about)) {
            $response['status'] = 'error';
            $response['message'] = 'campo sobre obrigatório';
            return response()->json($response);
        }

        if (empty($request->image)) {
            $response['status'] = 'error';
            $response['message'] = 'campo imagem obrigatório';
            return response()->json($response);
        }

        $hasEmail = User::query()
            ->where('email', $request->email)
            ->first();

        if ($hasEmail) {
            $response['status'] = 'error';
            $response['message'] = 'Email já cadastrado, escolha outro';
            return response()->json($response);
        }

        $name = 'user_' . time() . '.png';
        $path = $request->image->storeAs('images', $name);
        if ($path) {
            $path = 'storage/' . $path;
        }
        else {
            $response['status'] = 'error';
            $response['message'] = 'erro ao tentar criar usuário';
            return response()->json($response);
        }

        $data = $request->all();

        $data['password'] = bcrypt($data['password']);

        // fill foreign fields
        $data['media_id'] = Media::create([
            'title'       => '',
            'subtitle'    => '',
            'url' => $path,
            'category_id' => MediaCategory::SITE_INFO
        ])->id;

        // create user
        if (User::create($data)) {
            $result['status'] = 'ok';
            $result['message'] = 'sucesso ao criar usuário';
            $result['redirect'] = route('admin.users');
            $result['data'] = $data;
        }
        else {
            $result['status'] = 'error';
            $result['message'] = 'erro ao tentar criar usuário';
        }

        return response()->json($result);
    }

    /**
     *
     */
    public function update(Request $request, $id)
    {
        $user = User::find($id);
        if (isset($request->image)) {
            $name = 'profile_' . $id . '.png';
            $path = $request->image->storeAs('images', $name);
            if ($path) {
                $path = 'storage/' . $path;
                if ($user->media) {
                    $user->media->url = $path;
                    $user->media->save();
                }
                else {
                    $media = Media::create([
                        'title'       => '',
                        'subtitle'    => '',
                        'url' => $path,
                        'category_id' => MediaCategory::SITE_INFO
                    ]);
                    $user->media_id = $media->id;
                    $user->save();
                }
            }
            else {
                $response['status'] = 'error';
                $response['message'] = 'falha ao fazer upload da imagem';
                return response()->json($response);
            }
        }

        if (isset($request->name)) {
            $user->name = $request->name;
        }
        else {
            $response['status'] = 'error';
            $response['message'] = 'Nome não encontrado';
            return response()->json($response);
        }

        if (isset($request->lastname)) {
            $user->lastname = $request->lastname;
        }
        else {
            $response['status'] = 'error';
            $response['message'] = 'Sobrenome não encontrado';
            return response()->json($response);
        }

        if (isset($request->email)) {
            $user->email = $request->email;
        }
        else {
            $response['status'] = 'error';
            $response['message'] = 'Email não encontrado';
            return response()->json($response);
        }

        if (isset($request->about)) {
            $user->about = $request->about;
        }
        else {
            $response['status'] = 'error';
            $response['message'] = 'Sobre não encontrado';
            return response()->json($response);
        }

        if (isset($request->password) && isset($request->_password)) {
            if ($request->password === $request->_password) {
                $user->password = bcrypt($request->password);
            }
            else {
                $response['status'] = 'warning';
                $response['message'] = 'senhas diferentes digitadas';
                return response()->json($response);
            }
        }

        if (Auth::id() == User::ADMIN) {
            if (!empty($request->level)) {
                $user->level = $request->level;
            } else {
                $response['status'] = 'error';
                $response['message'] = 'campo nível obrigatório';
                return response()->json($response);
            }
        }

        $result = [];
        if ($user->save()) {
            $result['status'] = 'ok';
            $result['redirect'] = (Auth::id() == User::ADMIN) ? route('admin.users') : route('admin.dashboard');
            $result['message'] = 'sucesso ao atualizar usuário';
        }
        else {
            $result['status'] = 'error';
            $result['message'] = 'erro ao atualizar usuário';
        }

        return response()->json($result);
    }

    /**
     *
     */
    public function delete($id)
    {
        $user = User::find($id);
        if ($user->delete()) {
            return response()->json([
                'status' => 'ok',
                'message' => 'sucesso ao deletar usuário'
            ]);
        }
        else {
            return response()->json([
                'status' => 'error',
                'message' => 'erro ao deletar usuário'
            ]);
        }
    }
}
