<?php

namespace App\Api\Steam;

use App\Framework\Exceptions\UnexpectedResponseException;
use App\Framework\Steam\Api\JsonResponseApiProvider;
use GuzzleHttp\Exception\GuzzleException;

class SteamIdInnerProvider extends JsonResponseApiProvider
{
    protected function getUrl()
    {
        return 'http://api.steampowered.com/ISteamUser/ResolveVanityURL/v0001/';
    }

    protected function getEssenceValue($response)
    {
        return $response['response'];
    }

    /**
     * @param $profileName
     * @return mixed
     * @throws UnexpectedResponseException
     * @throws GuzzleException
     */
    public function fetch($profileName)
    {
        $response = $this->getEssence(['vanityurl' => $profileName]);
        if ($response['message']??'' === 'No match') {
            return null;
        }

        return $response['steamid'] ?? null;
    }
}
