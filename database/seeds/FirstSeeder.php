<?php

use GuzzleHttp\Client;
use Illuminate\Database\Seeder;

class FirstSeeder extends Seeder
{
    private $guzClient;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->guzClient = new Client();
        $this->savePosts();
    }

    private function savePosts()
    {
        $posts = $this->getPosts();

        if ($posts) {
            foreach ($posts as $post) {

            }
        }
    }

    private function getPosts()
    {
        $uri = 'https://jsonplaceholder.typicode.com/posts';

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
