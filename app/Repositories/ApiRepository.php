<?php


namespace App\Repositories;


use App\Libs\MySession;
use GuzzleHttp\Client;

class ApiRepository
{
    private $guzClient;
    private $mySession;

    /**
     * WebsiteController constructor.
     */
    public function __construct()
    {
        $this->guzClient = new Client();
        $this->mySession = new MySession();
    }

    /**
     * Cadastra uma nova pessoa na api
     */
    public function authenticate($email, $password)
    {
        $uri = env('API_PREFIX') . 'auth/login';

        try {
            $data = [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ],
                'json' => [
                    'email' => $email,
                    'password' => $password
                ]
            ];

            $result = $this->guzClient->post($uri, $data);

            if ($result) {
                $result = json_decode($result->getBody()->getContents(), true)['PessoaSalvarResult'];

                if ($result['Codigo'] == 0) {
                    return [
                        'status' => 'ok',
                        'code' => $result['Codigo'],
                        'message' => $result['Descricao']
                    ];
                }
                else {
                    return [
                        'status' => 'error',
                        'code' => $result['Codigo'],
                        'message' => $result['Descricao']
                    ];
                }
            }
            else {
                return [
                    'status' => 'error',
                    'message' => 'Resultado não encontrado'
                ];
            }
        }
        catch (RequestException $e) {
            return [
                'status' => 'error',
                'message' => 'Erro no método'
            ];
        }

        return [
            'status' => 'error',
            'message' => 'Erro desconhecido'
        ];
    }


    /**
     * @return bool|mixed|\Psr\Http\Message\ResponseInterface
     */
    public function getPosts()
    {
        $uri = env('API_PREFIX') . 'posts';

        try {
            $result = $this->guzClient->get($uri);

            if ($result) {
                $result = json_decode($result->getBody()->getContents(), true);
                return array2Object($result);
            }
            else {
                return false;
            }
        }
        catch (RequestException $e) {
            return false;
        }

        return false;
    }


    /**
     * @return bool|mixed|\Psr\Http\Message\ResponseInterface
     */
    public function getSinglePost($id)
    {
        $uri = env('API_PREFIX') . 'posts/' . $id;

        try {
            $result = $this->guzClient->get($uri);

            if ($result) {
                $result = json_decode($result->getBody()->getContents(), true);
                return array2Object($result);
            }
            else {
                return false;
            }
        }
        catch (RequestException $e) {
            return false;
        }

        return false;
    }
}