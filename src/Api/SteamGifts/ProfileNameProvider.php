<?php

namespace App\Api\SteamGifts;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;

class ProfileNameProvider
{
    const BASE_URL = 'https://www.steamgifts.com/go/user/';

    /** @var Client */
    protected $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @param string $steamId
     * @return mixed|null
     * @throws GuzzleException
     */
    public function provide(string $steamId)
    {
        $steamId = trim($steamId);
        if (!$steamId) {
            return null;
        }

        $userPageUrl = @$this->client->request('GET', self::BASE_URL . $steamId, [
            RequestOptions::ALLOW_REDIRECTS => false,
            RequestOptions::HTTP_ERRORS => false,
            RequestOptions::HEADERS => [
                'User-agent' => 'PAGYWOSG',
            ],
        ])->getHeader('Location')[0];

        if (!$userPageUrl || !preg_match('~^/user/(.+)~', $userPageUrl, $m)) {
            return null;
        }

        return $m[1];
    }
}
