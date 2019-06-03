<?php

namespace App\Api\Steam;

use App\Api\Steam\Schema\RecentlyPlayed;
use App\Framework\Exceptions\UnexpectedResponseException;
use App\Framework\Steam\Api\JsonResponseApiProvider;
use GuzzleHttp\Exception\GuzzleException;

class RecentlyPlayedApiProvider extends JsonResponseApiProvider
{
    protected function getUrl()
    {
        return 'https://api.steampowered.com/IPlayerService/GetRecentlyPlayedGames/v1/';
    }

    protected function getEssenceValue($response)
    {
        return $response['response']['games'];
    }

    /**
     * @param string $steamId
     * @return RecentlyPlayed[]
     * @throws UnexpectedResponseException
     * @throws GuzzleException
     */
    public function getList(string $steamId)
    {
        /** @var RecentlyPlayed[] $plays */
        $plays = $this->deserializeCollection($this->getEssence(['steamid' => $steamId]), RecentlyPlayed::class);

        return $plays;
    }
}
