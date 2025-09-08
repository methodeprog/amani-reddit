<?php

namespace Amani\Reddit;

use GuzzleHttp\Client;

class RedditClient
{
    private $client;
    private $accessToken;

    public function __construct(string $accessToken)
    {
        $this->client = new Client([
            'base_uri' => 'https://oauth.reddit.com/',
        ]);
        $this->accessToken = $accessToken;
    }

    public function submitPost(string $subreddit, string $title, string $url = null, string $text = null)
    {
        $data = [
            "sr"     => $subreddit,
            "title"  => $title,
            "kind"   => $url ? "link" : "self",
        ];

        if ($url) {
            $data["url"] = $url;
        } elseif ($text) {
            $data["text"] = $text;
        }

        $response = $this->client->post("api/submit", [
            'headers' => [
                'Authorization' => "Bearer {$this->accessToken}",
                'User-Agent'    => 'AmaniReddit/1.0'
            ],
            'form_params' => $data
        ]);

        return json_decode($response->getBody()->getContents(), true);
    }
}
