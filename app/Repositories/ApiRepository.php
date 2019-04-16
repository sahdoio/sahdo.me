<?php


namespace App\Repositories;


use App\Libs\MySession;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;

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
                $result = json_decode($result->getBody()->getContents(), true)['token'];
                return $result;
            }
            else {
                return false;
            }
        }
        catch (\Exception $e) {
            return false;
        }
        catch (RequestException $e) {
            return false;
        }
        catch (ClientException $e) {
            return false;
        }
        catch (ServerException $e) {
            return false;
        }

        return false;
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
        catch (\Exception $e) {
            return false;
        }
        catch (RequestException $e) {
            return false;
        }
        catch (ClientException $e) {
            return false;
        }
        catch (ServerException $e) {
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
        catch (\Exception $e) {
            return false;
        }
        catch (RequestException $e) {
            return false;
        }
        catch (ClientException $e) {
            return false;
        }
        catch (ServerException $e) {
            return false;
        }

        return false;
    }

    /**
     * @return bool|mixed|\Psr\Http\Message\ResponseInterface
     */
    public function getPostComments($id)
    {
        $uri = env('API_PREFIX') . 'posts/' . $id . '/comments';

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
        catch (\Exception $e) {
            return false;
        }
        catch (RequestException $e) {
            return false;
        }
        catch (ClientException $e) {
            return false;
        }
        catch (ServerException $e) {
            return false;
        }

        return false;
    }


    /**
     * @return bool|mixed|\Psr\Http\Message\ResponseInterface
     */
    public function savePost($params)
    {
        $uri = env('API_PREFIX') . 'posts/' . $params['id'];

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
        catch (\Exception $e) {
            return false;
        }
        catch (RequestException $e) {
            return false;
        }
        catch (ClientException $e) {
            return false;
        }
        catch (ServerException $e) {
            return false;
        }

        return false;
    }
}