<?php

namespace App\Api\Steam;

use App\Api\Steam\Schema\OwnedGame;
use App\Framework\Exceptions\UnexpectedResponseException;
use App\Framework\Steam\Api\JsonResponseApiProvider;
use GuzzleHttp\Exception\GuzzleException;

class OwnedGamesInnerProvider extends JsonResponseApiProvider
{
    /**
     * @param $steamUserId
     * @param array $appIds
     * @return OwnedGame[]
     * @throws GuzzleException
     * @throws UnexpectedResponseException
     */
    public function fetch($steamUserId, array $appIds)
    {
        $ownedGames = [];

        $responseGames = $this->getEssence([
            'steamid' => $steamUserId,
            'include_appinfo' => 1,
            'include_played_free_games' => 1,
            'appids_filter' => $appIds,
        ]);

        foreach ($responseGames as $ownedGame) {
            $ownedGames[] = (new OwnedGame)
                ->setName($ownedGame['name'])
                ->setAppId($ownedGame['appid'])
                ->setPlaytimeForever($ownedGame['playtime_forever']);
        }

        return $ownedGames;
    }

    protected function getUrl()
    {
        return 'https://api.steampowered.com/IPlayerService/GetOwnedGames/v1/';
    }

    protected function getEssenceValue($response)
    {
        return $response['response']['games'];
    }
}
