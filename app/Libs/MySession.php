<?php

namespace App\Libs;

use Session;
use DateTime;

class MySession
{
    private $session = 'sahdo_me_admin';
    private $cache = 1440; // minutes - default value = 24 horas

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
        
        // check id session time is expired
        //$session_data = Session::get($this->session);
        //if (!$this->checkSessionTime($session_data['start']))
        //    return false;

        return true;
    }

    /**
     * @param $datetime
     * @return bool
     * @throws \Exception
     */
    private function checkSessionTime($datetime)
    {
        $date = new DateTime($datetime);
        $timestamp = $date->getTimestamp();
        $now = time();
        $diff = $now - $this->cache * 60;

        if ($diff > $timestamp)
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
