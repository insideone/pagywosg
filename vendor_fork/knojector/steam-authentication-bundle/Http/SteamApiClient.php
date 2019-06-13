<?php

namespace Knojector\SteamAuthenticationBundle\Http;

use Knojector\SteamAuthenticationBundle\Exception\InvalidApiResponseException;
use GuzzleHttp\Client;

/**
 * @author Knojector <dev@404-labs.xyz>
 */
class SteamApiClient
{
    const STEAM_API = 'https://api.steampowered.com';

    /**
     * @var string
     */
    private $apiKey;

    /**
     * @var Client
     */
    private $client;

    /**
     * @param string $apiKey
     */
    public function __construct(string $apiKey)
    {
        $this->apiKey = $apiKey;
        $this->client = new Client();
    }

    /**
     * @param int $steamId
     *
     * @return array
     *
     * @throws InvalidApiResponseException
     */
    public function loadProfile(int $steamId)
    {
        $url = sprintf('%s/ISteamUser/GetPlayerSummaries/v0002/?key=%s&steamids=%s', self::STEAM_API, $this->apiKey, $steamId);

        $data = json_decode($this->client->get($url)->getBody()->getContents(), true);

        if (!isset($data['response']) || !isset($data['response']['players'])) {
            throw new InvalidApiResponseException('The received API response is invalid.');
        }

        $userData = current($data['response']['players']);
        if (false === $userData) {
            throw new InvalidApiResponseException('The received API response does not contain a user.');
        }

        return $userData;
    }
}
