<?php

namespace Amani\Reddit;

use GuzzleHttp\Client;

class Reddit
{
    protected string $clientId;
    protected string $clientSecret;
    protected string $username;
    protected string $password;
    protected Client $http;
    protected ?string $accessToken = null;

    public function __construct(string $clientId, string $clientSecret, string $username, string $password)
    {
        $this->clientId     = $clientId;
        $this->clientSecret = $clientSecret;
        $this->username     = $username;
        $this->password     = $password;
        $this->http         = new Client(['base_uri' => 'https://oauth.reddit.com/']);
    }

    /**
     * Authentification et récupération du token
     */
    protected function authenticate(): void
    {
        if ($this->accessToken) {
            return;
        }

        $client = new Client();
        $response = $client->post('https://www.reddit.com/api/v1/access_token', [
            'auth' => [$this->clientId, $this->clientSecret],
            'form_params' => [
                'grant_type' => 'password',
                'username'   => $this->username,
                'password'   => $this->password
            ],
            'headers' => ['User-Agent' => 'AmaniReddit/1.0']
        ]);

        $data = json_decode((string) $response->getBody(), true);
        $this->accessToken = $data['access_token'];

        $this->http = new Client([
            'base_uri' => 'https://oauth.reddit.com/',
            'headers'  => [
                'Authorization' => "Bearer {$this->accessToken}",
                'User-Agent'    => 'AmaniReddit/1.0'
            ]
        ]);
    }

    /**
     * Publier un post texte
     */
    public function submitText(string $subreddit, string $title, string $text): array
    {
        $this->authenticate();

        $response = $this->http->post('api/submit', [
            'form_params' => [
                'sr'     => $subreddit,
                'title'  => $title,
                'text'   => $text,
                'kind'   => 'self'
            ]
        ]);

        return json_decode((string) $response->getBody(), true);
    }
}
