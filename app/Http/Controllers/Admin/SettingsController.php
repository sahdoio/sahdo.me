<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SettingsController extends Controller {

    /**
     *
     */
    public function user($user_id) {
        return view('admin.settings.user', [
            'user_id' => $user_id,
            'page' => 'settings-user',
            'page_title' => 'Perfil'
        ]);
    }

    /**
     * 
     */
    public function users() {
        return view('admin.settings.users', [
            'page' => 'settings-users',
            'page_title' => 'Usuários'
        ]);
    }

    /**
     * 
     */
    public function preferences() {        
        return view('admin.settings.preferences', [
            'page' => 'settings-preferences',
            'page_title' => 'Preferências'
        ]);
    }
}
