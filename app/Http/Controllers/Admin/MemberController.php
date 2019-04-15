<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Libs\AppUtils;
use App\Models\Address;
use App\Models\Contact;
use App\Models\Media;
use App\Models\MediaCategory;
use App\Models\Member;
use Illuminate\Http\Request;

class MemberController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function new()
    {
        return view('admin.members.new', [
            'page' => 'member-new',
            'page_title' => 'Novo Membro'
        ]);
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function members()
    {
        return view('admin.members.members', [
            'page' => 'members',
            'page_title' => 'Membros'
        ]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
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
            $result = Member::orderBy('created_at', 'desc')
                ->join('addresses', 'members.address_id', '=', 'addresses.id')
                ->join('contacts', 'members.contact_id', '=', 'contacts.id')
                ->select([
                    'members.*',
                    'addresses.city',
                    'addresses.state',
                    'contacts.cellphone',
                    'contacts.phone',
                    'contacts.email'
                ])
                ->where('name', 'like', '%' . $search . '%')
                ->orWhere('lastname', 'like', '%' . $search . '%')
                ->orWhere('jobtitle', 'like', '%' . $search . '%')
                ->orWhere('city', 'like', '%' . $search . '%')
                ->orWhere('state', 'like', '%' . $search . '%')
                ->orWhere('cellphone', 'like', '%' . $search . '%')
                ->orWhere('phone', 'like', '%' . $search . '%')
                ->orWhere('email', 'like', '%' . $search . '%')
                ->skip($start)
                ->take($length)
                ->get();

            $response['recordsTotal'] = count($result);
            $response['recordsFiltered'] = count($result);
        }
        else {
            $result = Member::orderBy('created_at', 'desc')
                ->skip($start)
                ->take($length)
                ->get();

            $response['recordsTotal'] = Member::count();
            $response['recordsFiltered'] = Member::count();
        }

        $i = 0;
        foreach ($result as $res) {
            $actions = '
                <div>
                    <div class="actions text-center">
                        <a href="' . route('admin.members.edit', $res->id) . '" class="btn btn-round btn-warning btn-icon btn-sm edit">
                            <i class="fa fa-edit"></i>                                        
                        </a>
                        <a href="' . route('admin.members.delete', $res->id) . '" class="btn btn-round btn-danger btn-icon btn-sm remove">
                            <i class="fa fa-minus"></i>                                        
                        </a>
                    </div>
                </td>
            ';

            $data = (object) [
                ++$i,
                $res->name,
                $res->lastname,
                $res->jobtitle,
                $res->address->city,
                $res->address->state,
                $res->contact->cellphone,
                $res->contact->phone,
                $res->contact->email,
                $actions
            ];
            $response['data'][] = $data;
        }
        return response()->json($response);
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($id)
    {
        $member = Member::find($id);
        return view('admin.members.edit', [
            'page' => 'member-edit',
            'page_title' => 'Editar Membro',
            'member' => $member
        ]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(Request $request)
    {
        $result = [];
        $data = $request->all();

        try {
            if (empty($request->name)) {
                $result['status'] = 'warning';
                $result['message'] = 'Preencha o nome';
                return response()->json($result);
            }

            if (empty($request->lastname)) {
                $result['status'] = 'warning';
                $result['message'] = 'Preencha o sobrenome';
                return response()->json($result);
            }

            if (empty($request->civil_status)) {
                $result['status'] = 'warning';
                $result['message'] = 'Preencha o estado civil';
                return response()->json($result);
            }

            // fill foreign fields
            $data['address_id'] = Address::create($data)->id;
            $data['contact_id'] = Contact::create($data)->id;

            if ($request->profile_image) {
                $name = time() . '_' . $request->profile_image->getClientOriginalName();
                $media_type = AppUtils::getMediaTypeFromFileExtension($request->profile_image->getClientOriginalExtension());
                $path = $request->profile_image->storeAs('media', $name);
                if ($path) {
                    $path = 'storage/' . $path;
                    $media = Media::create([
                        'title' => '',
                        'subtitle' => '',
                        'url' => $path,
                        'category_id' => MediaCategory::SITE_INFO,
                        'type_id' => $media_type
                    ]);

                    if ($media)
                        $data['media_id'] = $media->id;
                    else
                        $data['media_id'] = null;
                }
                else {
                    $response['status'] = 'error';
                    $response['message'] = 'erro ao tentar atualizar mídia';
                    return response()->json($response);
                }
            }

            // create member
            if (Member::create($data)) {
                $result['status'] = 'ok';
                $result['message'] = 'sucesso ao criar membro';
                $result['redirect'] = route('admin.members');
                $result['data'] = $data;
            }
            else {
                $result['status'] = 'error';
                $result['message'] = 'erro ao tentar criar membro';
            }
        }
        catch (\Exception $e) {
            $result['status'] = 'error';
            $result['message'] = 'erro: ' . $e->getMessage();
        }

        return response()->json($result);
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $result = [];
        $member = Member::find($id);
        if (!$member) {
            $result['status'] = 'error';
            $result['message'] = 'Membro não encontrado';
            return response()->json($result);
        }

        if (empty($request->name)) {
            $result['status'] = 'warning';
            $result['message'] = 'Preencha o nome';
            return response()->json($result);
        }

        if (empty($request->lastname)) {
            $result['status'] = 'warning';
            $result['message'] = 'Preencha o sobrenome';
            return response()->json($result);
        }

        if (empty($request->civil_status)) {
            $result['status'] = 'warning';
            $result['message'] = 'Preencha o estado civil';
            return response()->json($result);
        }

        if ($request->profile_image) {
            $name = time() . '_' . $request->profile_image->getClientOriginalName();
            $path = $request->profile_image->storeAs('media', $name);

            if ($path) {
                $path = 'storage/' . $path;
            }
            else {
                $response['status'] = 'error';
                $response['message'] = 'erro ao tentar atualizar mídia';
                return response()->json($response);
            }

            $media_type = AppUtils::getMediaTypeFromFileExtension($request->profile_image->getClientOriginalExtension());

            $media = Media::updateOrCreate(
                [
                    'id' => $member->media_id
                ],
                [
                    'url' => $path,
                    'category_id' => MediaCategory::SITE_INFO,
                    'type_id' => $media_type,
                ]
            );

            $member->media_id = $media->id;
        }

        $member->name = $request->name;
        $member->lastname = $request->lastname;
        $member->jobtitle = $request->jobtitle;
        $member->rg = $request->rg;
        $member->cpf = $request->cpf;
        $member->birth_date = $request->birth_date;
        $member->ministerial_function = $request->ministerial_function;
        $member->baptism_date = $request->baptism_date;
        $member->civil_status = $request->civil_status;
        $member->father_name = $request->father_name;
        $member->mother_name = $request->mother_name;
        $member->birth_city = $request->birth_city;
        $member->nationality = $request->nationality;

        $member->address->cep = $request->cep;
        $member->address->street = $request->street;
        $member->address->number = $request->number;
        $member->address->district = $request->district;
        $member->address->city = $request->city;
        $member->address->state = $request->state;
        $member->address->country = $request->country;
        $member->address->save();

        $member->contact->cellphone = $request->cellphone;
        $member->contact->phone = $request->phone;
        $member->contact->email = $request->email;
        $member->contact->save();

        if ($member->save()) {
            $result['status'] = 'ok';
            $result['redirect'] = route('admin.members');
            $result['message'] = 'sucesso ao atualzar membro';
        }
        else {
            $result['status'] = 'error';
            $result['message'] = 'erro ao atualizar membro';
        }

        return response()->json($result);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function delete($id)
    {
        $member = Member::find($id);
        if ($member && $member->delete()) {
            return response()->json([
                'status' => 'ok',
                'message' => 'sucesso ao deletar membro'
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'erro ao deletar membro'
        ]);
    }
}
