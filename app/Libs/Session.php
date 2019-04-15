<?php
/**
 * Created by PhpStorm.
 * User: lucas
 * Date: 11/12/18
 * Time: 09:31
 */

namespace App\Libs;

use App\Models\SessionControl;
use App\Models\SiteView;
use App\Models\SiteViewsAgent;

class Session
{
    private $date;
    private $cache;
    private $traffic;
    private $browser;
    private $session = 'sahdo_me_website';

    function __construct($cache = null)
    {
        session_start();
        $this->CheckSession($cache);
    }

    /**
     * Verifica e executa todos os métodos da classe!
     * @param null $cache
     */
    private function CheckSession($cache = null)
    {
        $this->date = date('Y-m-d');
        $this->cache = ((int) $cache ? $cache : 20);

        if (empty($_SESSION[$this->session])):
            $this->setSession();
        else:
            $this->sessionUpdate();
        endif;

        $this->date = null;
    }

    /*
     * ***************************************
     * ********   SESSÃO DO USUÁRIO   ********
     * ***************************************
     */

    /**
     * Inicia a sessão do usuário
     */
    private function setSession()
    {
        $_SESSION[$this->session] = [
            "session" => session_id(),
            "startview" => date('Y-m-d H:i:s'),
            "endview" => date('Y-m-d H:i:s', strtotime("+{$this->cache}minutes")),
            "ip" => $_SERVER['REMOTE_ADDR'],
            "url" => strip_tags(trim($_SERVER['REQUEST_URI'])),
            "agent" => $_SERVER['HTTP_USER_AGENT']
        ];
    }


    /**
     * Atualiza sessão do usuário!
     */
    private function sessionUpdate() {
        $_SESSION[$this->session]['endview'] = date('Y-m-d H:i:s', strtotime("+{$this->cache}minutes"));
        $_SESSION[$this->session]['url'] = strip_tags(trim($_SERVER['REQUEST_URI']));
    }

    /*
     * ***************************************
     * *** USUÁRIOS, VISITAS, ATUALIZAÇÕES ***
     * ***************************************
     */

    /**
     * Verifica e insere o tráfego na tabela
     */
    private function setTraffic()
    {
        $this->getTraffic();

        if (!$this->traffic):
            $siteView = SiteView::create([
                'date' => $this->date,
                'users' => 1,
                'views' => 1,
                'pages' => 1
            ]);
        else:
            $siteView = SiteView::where('date', $this->date)->first();
            if ($siteView) {
                if (!$this->getCookie()):
                    $siteView->users = $this->traffic->users + 1;
                    $siteView->views = $this->traffic->views + 1;
                    $siteView->pages = $this->traffic->pages + 1;
                else:
                    $siteView->views = $this->traffic->views + 1;
                    $siteView->pages = $this->traffic->pages + 1;
                endif;

                $siteView->save();
            }
            else {
                return false;
            }
        endif;

        if ($siteView)
            return true;

        return false;
    }

    /**
     * Verifica e atualiza os pageviews
     */
    private function TrafficUpdate()
    {
        $this->getTraffic();

        $siteView = SiteView::where('date', $this->date)->first();

        if ($siteView) {
            $siteView->pages = $this->traffic->pages + 1;
            $siteView->save();
        }


        $this->traffic = null;
    }

    /**
     * Obtém dados da tabele [ HELPER TRAFFIC ]
     * kod_siteviews
     * @return bool
     */
    private function getTraffic()
    {
        $siteView = SiteView::where('date', $this->date)->first();

        if ($siteView)
            $this->traffic = $siteView;

        return false;
    }

    /**
     * Verifica, cria e atualiza o cookie do usuário [ HELPER TRAFFIC ]
     * @return bool
     */
    private function getCookie()
    {
        $Cookie = filter_input(INPUT_COOKIE, $this->session, FILTER_DEFAULT);
        setcookie("useronline", base64_encode("drekod"), time() + 86400, null, null);

        if (!$Cookie)
            return false;

        return true;
    }

    /*
     * ***************************************
     * *******  NAVEGADORES DE ACESSO   ******
     * ***************************************
     */

    /**
     * Identifica navegador do usuário!
     */
    private function CheckBrowser()
    {
        $this->browser = $_SESSION[$this->session]['agent'];
        if (strpos($this->browser, 'Chrome')):
            $this->browser = 'Chrome';
        elseif (strpos($this->browser, 'Firefox')):
            $this->browser = 'Firefox';
        elseif (strpos($this->browser, 'MSIE') || strpos($this->browser, 'Trident/')):
            $this->browser = 'IE';
        else:
            $this->browser = 'Outros';
        endif;

        return true;
    }

    /**
     * Atualiza tabela com dados de navegadores!
     */
    private function BrowserUpdate()
    {
        $siteViewsAgent = SiteViewsAgent::where('agent_name', $this->browser)->first();

        if ($siteViewsAgent) {
            $siteViewsAgent->agent_views = $siteViewsAgent->agent_views + 1;
            $siteViewsAgent->agent_lastview = date('Y-m-d H:i:s');
            $siteViewsAgent->save();
        }
        else {
            $siteViewsAgent = SiteViewsAgent::create([
                'agent_name' => $this->browser,
                'agent_views' => 1,
                'agent_lastview' => date('Y-m-d H:i:s')
            ]);
        }

        return true;
    }

    /*
     * ***************************************
     * *********   USUÁRIOS ONLINE   *********
     * ***************************************
     */

    /**
     * Cadastra usuário online na tabela!
     */
    private function setUsuario()
    {
        $data = $_SESSION[$this->session];
        $data['agent_name'] = $this->browser;

        $sessionControl = SessionControl::create($data);

        if ($sessionControl)
            return true;

        return false;
    }

    /**
     * Atualiza navegação do usuário online!
     */
    private function UsuarioUpdate()
    {
        $sessionControl = SessionControl::where('session', $_SESSION[$this->session]['session'])->first();

        if ($sessionControl) {
            $sessionControl->endview = $_SESSION[$this->session]['endview'];
            $sessionControl->url = $_SESSION[$this->session]['url'];
            $this->setUsuario();
        }
    }
}
