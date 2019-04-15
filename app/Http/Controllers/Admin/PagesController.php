<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Libs\AppUtils;
use App\Models\Icon;
use App\Models\Media;
use App\Models\MediaCategory;
use App\Models\MediaType;
use App\Models\SiteInfo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class PagesController extends Controller
{
    /*
    ####################################
    # Home Area
    ####################################
    */

    /**
     *
     */
    public function home()
    {
        $data = [
            'site_info' => SiteInfo::first(),
            'icons' => Icon::all(),
            'page' => 'home',
            'page_title' => 'Home',
        ];

        return view('admin.pages.home', $data);
    }

    /**
     *
     */
    public function update_home(Request $request)
    {
        $siteinfo = SiteInfo::all()->first();

        if (isset($request->company_description))
            $siteinfo->company_description = $request->company_description;

        if (isset($request->differential_1_title))
            $siteinfo->differential_1_title = $request->differential_1_title;

        if (isset($request->differential_1_icon))
            $siteinfo->differential_1_icon_id = $request->differential_1_icon;

        if (isset($request->differential_1_content))
            $siteinfo->differential_1_content = $request->differential_1_content;

        if (isset($request->differential_2_title))
            $siteinfo->differential_2_title = $request->differential_2_title;

        if (isset($request->differential_2_icon))
            $siteinfo->differential_2_icon_id = $request->differential_2_icon;

        if (isset($request->differential_2_content))
            $siteinfo->differential_2_content = $request->differential_4_content;

        if (isset($request->differential_3_title))
            $siteinfo->differential_3_title = $request->differential_3_title;

        if (isset($request->differential_3_icon))
            $siteinfo->differential_3_icon_id = $request->differential_3_icon;

        if (isset($request->differential_3_content))
            $siteinfo->differential_3_content = $request->differential_3_content;

        if (isset($request->differential_4_title))
            $siteinfo->differential_4_title = $request->differential_4_title;

        if (isset($request->differential_4_icon))
            $siteinfo->differential_4_icon_id = $request->differential_4_icon;

        if (isset($request->differential_4_content))
            $siteinfo->differential_4_content = $request->differential_4_content;

        $siteinfo->save();

        $response = $siteinfo->toArray();
        $response['status'] = 'ok';
        $response['message'] = "success";
        $response['redirect'] = route('admin.pages.home');

        return response()->json($response);
    }

    /*
    ####################################
    # About Area
    ####################################
    */

    /**
     *
     */
    public function about()
    {
        $data = [
            'site_info' => SiteInfo::first(),
            'page' => 'about',
            'page_title' => 'Quem Somos',
        ];

        return view('admin.pages.about', $data);
    }

    /**
     *
     */
    public function update_about(Request $request)
    {
        $response = [];
        $files = $request->file();
        $siteInfo = SiteInfo::first();

        if (!isset($request->about_bio_title) || !isset($request->about_bio)) {
            $response['status'] = 'warning';
            $response['message'] = 'preencha todos os campos';
            return response()->json($response);
        }

        if ($siteInfo) {
            if (count($files) > 0) {
                foreach ($files as $key => $file) {
                    if ($key == 'profile_image') {
                        $media = $siteInfo->profile_image;
                    }
                    elseif ($key == 'about_banner') {
                        $media = $siteInfo->about_banner;
                    }
                    else {
                        continue;
                    }

                    $name = time() . '_' . $file->getClientOriginalName();
                    $path = $file->storeAs('media', $name);
                    if ($path) {
                        $path = 'storage/' . $path;
                    }
                    else {
                        $response['status'] = 'error';
                        $response['message'] = 'erro ao tentar atualizar mídia';
                        return response()->json($response);
                    }

                    $media_type = AppUtils::getMediaTypeFromFileExtension($file->getClientOriginalExtension());
                    if ($media) {
                        $file_path = public_path() . '/' . $media->url;
                        if (file_exists($file_path))
                            File::delete($file_path);
                        $media->url = $path;
                        $media->type_id = $media_type;
                        $media->save();
                    }
                    else {
                        $media = Media::create([
                            'title' => '',
                            'subtitle' => '',
                            'url' => $path,
                            'category_id' => MediaCategory::SITE_INFO,
                            'type_id' => $media_type
                        ]);
                    }
                }
            }

            /*
             * input info
             */

            if ($request->about_bio_title)
                $siteInfo->about_bio_title = $request->about_bio_title;

            if ($request->about_bio)
                $siteInfo->about_bio = $request->about_bio;

            if ($request->about_reel) {
                $reel = $siteInfo->about_reel;

                $media_type = MediaType::GENERIC;
                if (str_contains($request->about_reel, 'vimeo'))
                    $media_type = MediaType::VIMEO;
                if (str_contains($request->about_reel, 'youtu'))
                    $media_type = MediaType::YOUTUBE;

                if ($reel) {
                    $reel->url = $request->about_reel;
                    $reel->type_id = $media_type;
                    $reel->save();
                }
                else {
                    $reel = Media::create([
                        'title' => '',
                        'subtitle' => '',
                        'url' => $request->about_reel,
                        'category_id' => MediaCategory::SITE_INFO,
                        'type_id' => $media_type
                    ]);
                }
            }

            $siteInfo->save();
            $response = $siteInfo->toArray();
            $response['status'] = 'ok';
            $response['message'] = 'sucesso ao atualizar informações';
            $response['redirect'] = route('admin.pages.about');
        }
        else {
            $response['status'] = 'error';
            $response['message'] = 'erro ao tentar atualizar as informações';
        }

        return response()->json($response);
    }

    /*
    ####################################
    # Contact Area
    ####################################
    */

    /**
     *
     */
    public function contact()
    {
        $data = [
            'site_info' => SiteInfo::join('medias as m_mission', 'site_info.mission_image', '=', 'm_mission.id')
                ->join('medias as m_vision', 'site_info.vision_image', '=', 'm_vision.id')
                ->join('medias as m_values', 'site_info.values_image', '=', 'm_values.id')
                ->select('site_info.*', 'm_mission.url as mission_image', 'm_vision.url as vision_image', 'm_values.url as values_image')
                ->first(),
            'page' => 'contact',
            'page_title' => 'Contato',
        ];

        return view('admin.pages.contact', $data);
    }

    /**
     *
     */
    public function update_contact(Request $request)
    {
        $siteinfo = SiteInfo::all()->first();

        if (isset($request->cellphone))
            $siteinfo->contact->cellphone = $request->cellphone;

        if (isset($request->ceplphone2))
            $siteinfo->contact->cellphone2 = $request->cellphone2;

        if (isset($request->email))
            $siteinfo->contact->email = $request->email;

        if (isset($request->email2))
            $siteinfo->contact->email2 = $request->email2;

        if (isset($request->facebook))
            $siteinfo->contact->facebook = $request->facebook;

        if (isset($request->instagram))
            $siteinfo->contact->instagram = $request->instagram;

        if (isset($request->youtube))
            $siteinfo->contact->youtube = $request->youtube;

        if (isset($request->flickr))
            $siteinfo->contact->flickr = $request->flickr;

        if (isset($request->twitter))
            $siteinfo->contact->twitter = $request->twitter;

        $siteinfo->save();
        $siteinfo->contact->save();

        $response = $siteinfo->toArray();
        $response['status'] = 'ok';
        $response['message'] = "success";
        $response['redirect'] = "/admin/pages/contact";

        return response()->json($response);
    }
}
