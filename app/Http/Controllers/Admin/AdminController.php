<?php

namespace App\Http\Controllers;

use App\Models\Media;
use Illuminate\Http\Request;
use App\Models\SiteInfo;


class AdminController extends Controller 
{
    /**
     * 
     */
    public function notfound() 
    {        
        return view('pages.errors.404');
    }    
}
