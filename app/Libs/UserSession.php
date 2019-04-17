<?php

namespace App\Libs;

use Session;

class UserSession
{
    protected $session = 'user_website';

    /**
     * @param $token
     * @return bool|mixed
     */
    public function setSession($token)
    {
        Session::put($this->session, [
            "session" => session_id(),
            "start" => date('Y-m-d H:i:s'),
            "ip" => $_SERVER['REMOTE_ADDR'],
            "url" => strip_tags(trim($_SERVER['REQUEST_URI'])),
            "agent" => $_SERVER['HTTP_USER_AGENT'],
            "jwt" => $token
        ]);

        return $this->getSession();
    }

    /**
     *
     */
    public function endSession()
    {
        Session::forget($this->session);
    }

    /**
     * @return bool
     */
    public function checkSession()
    {
        // check if session exists
        if (null === Session::get($this->session))
            return false;

        return true;
    }

    /**
     * @return bool|mixed
     */
    public function getSession()
    {
        if (empty($_SESSION[$this->session])) {
            return false;
        }

        return $_SESSION[$this->session];
    }
}
