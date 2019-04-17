<?php


namespace App\Repositories;


use App\Libs\AdminUserSession;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use Session;

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
        $this->mySession = new AdminUserSession();
    }

    /**
     * Autentica um usuário na api
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
                    'type' => 1, // normal user
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
    }

    /**
     * Autentica um usuário administrador na api
     */
    public function admin_authenticate($email, $password)
    {
        $uri = env('API_PREFIX') . 'auth/login';

        try {
            $data = [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ],
                'json' => [
                    'type' => 2, // admin user
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
    }

    /**
     * @return bool
     */
    public function tokenVerify()
    {
        return true;
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
    }


    /**
     * @return bool|mixed|\Psr\Http\Message\ResponseInterface
     */
    public function getSinglePost($post_id)
    {
        $uri = env('API_PREFIX') . 'posts/' . $post_id;

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
    }

    /**
     * @return bool|mixed|\Psr\Http\Message\ResponseInterface
     */
    public function getPostComments($post_id)
    {
        $uri = env('API_PREFIX') . 'posts/' . $post_id . '/comments';

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
    }

    /**
     * @return bool|mixed|\Psr\Http\Message\ResponseInterface
     */
    public function newPost($params)
    {
        $token = Session::get('user_admin')['jwt'];
        $uri = env('API_PREFIX') . 'posts';

        try {
            $data = [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ],
                'json' => [
                    'token' => $token,
                    'title' => $params->title,
                    'body' => $params->body
                ]
            ];

            $result = $this->guzClient->post($uri, $data);

            if ($result) {
                $result = json_decode($result->getBody()->getContents(), true);
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
    }

    /**
     * @return bool|mixed|\Psr\Http\Message\ResponseInterface
     */
    public function updatePost($post_id, $params)
    {
        $token = Session::get('user_admin')['jwt'];
        $uri = env('API_PREFIX') . 'posts/' . $post_id;

        try {
            $data = [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ],
                'json' => [
                    'token' => $token,
                    'title' => $params->title,
                    'body' => $params->body
                ]
            ];

            $result = $this->guzClient->post($uri, $data);

            if ($result) {
                $result = json_decode($result->getBody()->getContents(), true);
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
    }

    /**
     * @return bool|mixed|\Psr\Http\Message\ResponseInterface
     */
    public function deletePost($post_id)
    {
        $token = Session::get('user_admin')['jwt'];
        $uri = env('API_PREFIX') . 'posts/' . $post_id . '/delete';

        try {
            $data = [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ],
                'json' => [
                    'token' => $token
                ]
            ];

            $result = $this->guzClient->get($uri, $data);

            if ($result) {
                $result = json_decode($result->getBody()->getContents(), true);
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
    }



    /**
     * @return bool|mixed|\Psr\Http\Message\ResponseInterface
     */
    public function newComment($post_id, $params)
    {
        $uri = env('API_PREFIX') . 'posts/' . $post_id . '/comments';
        try {
            $data = [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ],
                'json' => [
                    'name' => $params->name,
                    'email' => $params->email,
                    'body' => $params->comment
                ]
            ];

            $result = $this->guzClient->post($uri, $data);

            if ($result) {
                $result = json_decode($result->getBody()->getContents(), true);
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
    }
}