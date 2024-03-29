<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\ApiRepository;
use Illuminate\Http\Request;
use Session;
use App\Libs\AdminUserSession;

class LoginController extends Controller
{
    private $my_session;
    private $apiRepo;

    /**
     * LoginController constructor.
     * @param null $Cache
     */
    function __construct($Cache = null)
    {
        $this->my_session = new AdminUserSession();
        $this->apiRepo = new ApiRepository();
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function index()
    {
        if ($this->my_session->checkSession()) {
            return redirect()->route('admin.dashboard');
        }

        return view('admin.login');
    }

    /**
     * @param Request $req
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function in(Request $req)
    {
        $data = $req->all();
        $token = $this->validateLogin($data['email'], $data['password']);
        if ($token) {
            $this->my_session->setSession($token);
            return redirect()->route('admin.dashboard');
        }
        return view('admin.login');
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function out()
    {
        $this->my_session->endSession();
        return redirect()->route('admin.login');
    }

    /**
     * @param $email
     * @param $password
     * @return bool
     */
    private function validateLogin($email, $password)
    {
        return $this->apiRepo->admin_authenticate($email, $password);
    }
}
