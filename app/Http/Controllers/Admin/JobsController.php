<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Job;
use App\Models\JobMedia;
use App\Models\Media;
use App\Models\MediaCategory;
use App\Models\MediaType;
use App\Models\SiteInfo;
use App\Libs\AppUtils;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Session;

class JobsController extends Controller
{
    /*
    ###########################
    # View Area
    ###########################
    */

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function jobs()
    {
        $data = [
            'page' => 'jobs',
            'page_title' => 'Jobs',
            'jobs' => Job::all(),
            'site_info' => SiteInfo::all()->first()
        ];

        return view('admin.jobs.jobs', $data);
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function new()
    {
        $types = MediaType::where('id', MediaType::JPG)
            ->orWhere('id', MediaType::PNG)
            ->orWhere('id', MediaType::GIF)
            ->orWhere('id', MediaType::VIMEO)
            ->get();

        $data  = [
            'page' => 'jobs-new',
            'page_title' => 'Novo job',
            'clients' => Client::all(),
            'types' => $types
        ];

        return view('admin.jobs.new', $data);
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function edit($id)
    {
        $job = Job::find($id);

        $types = MediaType::where('id', MediaType::JPG)
            ->orWhere('id', MediaType::PNG)
            ->orWhere('id', MediaType::GIF)
            ->orWhere('id', MediaType::VIMEO)
            ->get();

        if (!$job) {
            Session::flash('flash_message', 'job não encontrado');
            return redirect()->route('admin.jobs');
        }

        $data = [
            'page' => 'jobs-edit',
            'page_title' => 'Editar job',
            'job' => $job,
            'clients' => Client::all(),
            'types' => $types
        ];

        return view('admin.jobs.edit', $data);
    }

    /*
    ###########################
    # Ajax Area
    ###########################
    */

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(Request $request)
    {
        $response = [];
        $input = $request->input();
        $files = $request->file();

        if (empty($request->title) || !isset($request->description)) {
            $response['status'] = 'warning';
            $response['message'] = 'preencha todos os campos';
            return response()->json($response);
        }

        $check_media = false;
        if (count($files) <= 0) {
            foreach ($input as $key => $item) {
                if (str_contains($key, 'media')) {
                    $check_media = true;
                }
            }
        }
        else {
            $check_media = true;
        }

        if (!$check_media) {
            $response['status'] = 'warning';
            $response['message'] = 'Mídia pendente!';
            return response()->json($response);
        }

        $job = Job::create([
            'title' => $request->title,
            'description' => $request->description,
            'client_id' => (isset($request->client_id) && !empty($request->client_id)) ? $request->client_id : null,
            'date' => date('Y-m-d', strtotime($request->date)),
            'cover_media_id' => null
        ]);

        if ($job) {
            // get medias from request
            if (count($input) > 0) {
                foreach ($input as $key => $item) {
                    if (str_contains($key, 'media')) {
                        $position = AppUtils::getOnlyNumbers($key);

                        $jobMedia = JobMedia::where('job_id', $job->id)
                            ->where('position', $position)
                            ->first();

                        $media_type = MediaType::GENERIC;
                        if (str_contains($item, 'vimeo'))
                            $media_type = MediaType::VIMEO;
                        if (str_contains($item, 'youtu'))
                            $media_type = MediaType::YOUTUBE;

                        if ($jobMedia) {
                            $jobMedia->media->url = $item;
                            $jobMedia->media->type_id = $media_type;
                            $jobMedia->media->save();
                        }
                        else {
                            $jobMedia = JobMedia::create([
                                'job_id' => $job->id,
                                'media_id' => Media::create([
                                    'title' => '',
                                    'subtitle' => '',
                                    'url' => $item,
                                    'category_id' => MediaCategory::JOB,
                                    'type_id' => $media_type
                                ])->id,
                                'position' => $position
                            ]);
                        }
                    }
                }
            }

            // get media from files
            if (count($files) > 0) {
                foreach ($files as $key => $file) {
                    $position = AppUtils::getOnlyNumbers($key);
                    $name = time() . '_' . $file->getClientOriginalName();
                    $path = $file->storeAs('media', $name);
                    if ($path) {
                        $path = 'storage/' . $path;
                    }
                    else {
                        $response['status'] = 'error';
                        $response['message'] = 'erro ao tentar criar job';
                        return response()->json($response);
                    }

                    $jobMedia = JobMedia::where('job_id', $job->id)
                        ->where('position', $position)
                        ->first();

                    $media_type = AppUtils::getMediaTypeFromFileExtension($file->getClientOriginalExtension());
                    if ($jobMedia) {
                        $file_path = public_path() . '/' . $jobMedia->media->url;
                        if (file_exists($file_path))
                            File::delete($file_path);
                        $jobMedia->media->url = $path;
                        $jobMedia->media->type_id = $media_type;
                        $jobMedia->media->save();
                    }
                    else {
                        $jobMedia = JobMedia::create([
                            'job_id' => $job->id,
                            'media_id' => Media::create([
                                'title' => '',
                                'subtitle' => '',
                                'url' => $path,
                                'category_id' => MediaCategory::JOB,
                                'type_id' => $media_type
                            ])->id,
                            'position' => $position
                        ]);
                    }
                }
            }

            /*
             * cover image
             */

            $firstMedia = JobMedia::where('job_id', $job->id)
                ->orderBy('position')
                ->first();

            if ($firstMedia)
                $job->cover_media_id = $firstMedia->media->id;

            $job->save();
            $response = $job->toArray();
            $response['status'] = 'ok';
            $response['message'] = 'sucesso ao criar job';
            $response['redirect'] = route('admin.jobs');
        }
        else {
            $response['status'] = 'error';
            $response['message'] = 'erro ao tentar criar job';
        }

        return response()->json($response);
    }

    /**
     *
     */
    public function update_info(Request $request)
    {
        $siteinfo = SiteInfo::all()->first();

        if (isset($request->jobs_main_text))
            $siteinfo->jobs_main_text = $request->jobs_main_text;

        $siteinfo->save();

        $response = $siteinfo->toArray();
        $response['status'] = 'ok';
        $response['message'] = "success";
        $response['redirect'] = route('admin.jobs');

        return response()->json($response);
    }

    /**
     *
     */
    public function update(Request $request, $id)
    {
        $response = [];
        $input = $request->input();
        $files = $request->file();
        $job = Job::find($id);

        if (!isset($request->title) || !isset($request->description)) {
            $response['status'] = 'warning';
            $response['message'] = 'preencha todos os campos';
            return response()->json($response);
        }

        /*
         * para o update pode sim haver mídia ausente,
         * pois o dropify não manda arquivos em caso de
         * não haver alteração da mídia já existente.
         * Apenas em caso de delete que precisamos checar.
         */

        $delete_media = false;
        foreach ($input as $key => $item) {
            if (str_contains($key, 'delete')) {
                $delete_media = true;
            }
        }

        if ($delete_media) {
            $check_media = false;
            if (count($files) <= 0) {
                foreach ($input as $key => $item) {
                    if (str_contains($key, 'media')) {
                        $check_media = true;
                    }
                }
            } else {
                $check_media = true;
            }

            if (!$check_media) {
                $response['status'] = 'warning';
                $response['message'] = 'Mídia pendente!';
                return response()->json($response);
            }
        }

        if ($job) {
            // delete medias if needed
            if (count($input) > 0) {
                foreach ($input as $key => $item) {
                    if (str_contains($key, 'delete')) {
                        $jobMedia = JobMedia::where('job_id', $job->id)
                            ->where('media_id', $item)
                            ->first();

                        if ($jobMedia) {
                            $file_path = public_path() . '/' . $jobMedia->media->url;
                            if (file_exists($file_path))
                                File::delete($file_path);
                            $jobMedia->media->delete();
                            $jobMedia->delete();
                        }
                    }
                }
            }

            // refresh positions
            foreach ($job->jobMedias()->get() as $i => $jobMedia) {
                $jobMedia->position = $i + 1;
                $jobMedia->save();
            }

            // get medias from request
            if (count($input) > 0) {
                foreach ($input as $key => $item) {
                    if (str_contains($key, 'media')) {
                        $position = AppUtils::getOnlyNumbers($key);

                        $jobMedia = JobMedia::where('job_id', $job->id)
                            ->where('position', $position)
                            ->first();

                        $media_type = MediaType::GENERIC;
                        if (str_contains($item, 'vimeo'))
                            $media_type = MediaType::VIMEO;
                        if (str_contains($item, 'youtu'))
                            $media_type = MediaType::YOUTUBE;
                        if ($jobMedia) {
                            $jobMedia->media->url = $item;
                            $jobMedia->media->type_id = $media_type;
                            $jobMedia->media->save();
                        }
                        else {
                            $jobMedia = JobMedia::create([
                                'job_id' => $job->id,
                                'media_id' => Media::create([
                                    'title' => '',
                                    'subtitle' => '',
                                    'url' => $item,
                                    'category_id' => MediaCategory::JOB,
                                    'type_id' => $media_type
                                ])->id,
                                'position' => $position
                            ]);
                        }
                    }
                }
            }

            // get media from files
            if (count($files) > 0) {
                foreach ($files as $key => $file) {
                    $position = AppUtils::getOnlyNumbers($key);
                    $name = time() . '_' . $file->getClientOriginalName();
                    $path = $file->storeAs('media', $name);
                    if ($path) {
                        $path = 'storage/' . $path;
                    }
                    else {
                        $response['status'] = 'error';
                        $response['message'] = 'erro ao tentar criar job';
                        return response()->json($response);
                    }

                    $jobMedia = JobMedia::where('job_id', $job->id)
                        ->where('position', $position)
                        ->first();

                    $media_type = AppUtils::getMediaTypeFromFileExtension($file->getClientOriginalExtension());
                    if ($jobMedia) {
                        $file_path = public_path() . '/' . $jobMedia->media->url;
                        if (file_exists($file_path))
                            File::delete($file_path);

                        $jobMedia->media->url = $path;
                        $jobMedia->media->type_id = $media_type;
                        $jobMedia->media->save();
                    }
                    else {
                        $jobMedia = JobMedia::create([
                            'job_id' => $job->id,
                            'media_id' => Media::create([
                                'title' => '',
                                'subtitle' => '',
                                'url' => $path,
                                'category_id' => MediaCategory::JOB,
                                'type_id' => $media_type
                            ])->id,
                            'position' => $position
                        ]);
                    }
                }
            }

            /*
             * main data
             */

            if ($request->title)
                $job->title = $request->title;

            if ($request->description)
                $job->description = $request->description;

            if ($request->client_id)
                $job->client_id = $request->client_id;

            if ($request->date)
                $job->date = $request->date;

            // update cover image
            $firstMedia = JobMedia::where('job_id', $job->id)
                ->orderBy('position')
                ->first();

            if ($firstMedia)
                $job->cover_media_id = $firstMedia->media->id;

            $job->save();
            $response = $job->toArray();
            $response['status'] = 'ok';
            $response['message'] = 'sucesso ao atualizar job';
            $response['redirect'] = route('admin.jobs');
        }
        else {
            $response['status'] = 'error';
            $response['message'] = 'erro ao tentar atualizar job';
        }

        return response()->json($response);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function delete($id)
    {
        $job = Job::find($id);

        $jobMedias = JobMedia::where('job_id', $job->id)
            ->orderBy('position')
            ->get();

        foreach ($jobMedias as $jobMedia) {
            $file_path = public_path() . '/' . $jobMedia->media->url;
            if (file_exists($file_path))
                File::delete($file_path);
            $jobMedia->media->delete();
            $jobMedia->delete();
        }

        if ($job->delete()) {
            return response()->json([
                'status' => 'ok',
                'message' => 'sucesso ao deletar job',
                'redirect' => route('admin.jobs')
            ]);
        }
        else {
            return response()->json([
                'status' => 'error',
                'message' => 'erro ao deletar job'
            ]);
        }
    }
}
