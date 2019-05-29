<?php

namespace App\Api\Steam;

use App\Api\Steam\Schema\GetAppListResultApp;
use App\Framework\Exceptions\UnexpectedResponseException;
use App\Framework\Steam\Api\JsonResponseApiProvider;
use GuzzleHttp\Exception\GuzzleException;

class GameNamesInnerProvider extends JsonResponseApiProvider
{
    /**
     * @return GetAppListResultApp[]
     * @throws UnexpectedResponseException
     * @throws GuzzleException
     */
    public function fetch()
    {
        $apps = [];
        foreach ($this->getEssence() as $rawApp) {
            $app = new GetAppListResultApp;
            $app->name = $rawApp['name'];
            $app->appid = $rawApp['appid'];

            $apps[] = $app;
        }

        return $apps;
    }

    protected function getDefaults()
    {
        $defaults = parent::getDefaults();
        $defaults += ['format' => 'json'];
        return $defaults;
    }

    protected function getUrl()
    {
        return 'http://api.steampowered.com/ISteamApps/GetAppList/v0002/';
    }

    protected function getEssenceValue($response)
    {
        return $response['applist']['apps'];
    }
}
