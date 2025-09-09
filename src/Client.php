<?php

namespace Amani\Reddit;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\RequestException;

class Client
{
    protected $http;
    protected $accessToken;
    protected $userAgent;

    public function __construct(string $accessToken, string $userAgent = 'AmaniRedditBot/1.0')
    {
        $this->accessToken = $accessToken;
        $this->userAgent   = $userAgent;

        $this->http = new GuzzleClient([
            'base_uri' => 'https://oauth.reddit.com/',
            'headers' => [
                'Authorization' => "Bearer {$this->accessToken}",
                'User-Agent'    => $this->userAgent
            ]
        ]);
    }

    public function submitText(string $subreddit, string $title, string $text)
    {
        try {
            $response = $this->http->post("api/submit", [
                'form_params' => [
                    'sr'    => $subreddit,
                    'title' => $title,
                    'kind'  => 'self',
                    'text'  => $text
                ]
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (RequestException $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public function submitLink(string $subreddit, string $title, string $url)
    {
        try {
            $response = $this->http->post("api/submit", [
                'form_params' => [
                    'sr'    => $subreddit,
                    'title' => $title,
                    'kind'  => 'link',
                    'url'   => $url
                ]
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (RequestException $e) {
            return ['error' => $e->getMessage()];
        }
    }
}
