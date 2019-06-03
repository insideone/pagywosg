<?php

namespace App\Api\Steam;

use App\Api\Steam\Schema\GetAppListResultApp;
use App\Framework\Exceptions\UnexpectedResponseException;
use App\Framework\Steam\Api\JsonResponseApiProvider;
use DateTime;
use GuzzleHttp\Exception\GuzzleException;

class GameNamesApiProvider extends JsonResponseApiProvider
{
    /**
     * @param DateTime $modifiedSince
     * @return GetAppListResultApp[]
     * @throws GuzzleException
     * @throws UnexpectedResponseException
     */
    public function fetch(?DateTime $modifiedSince)
    {
        $request = [
            'include_games' => 1,
            'max_results' => 50000,
        ];

        if ($modifiedSince) {
            $request['if_modified_since'] = $modifiedSince->getTimestamp();
        }

        $apps = [];

        $response = $this->getEssence($request);

        if (!empty($response['apps'])) {
            foreach ($response['apps'] as $rawApp) {
                $app = new GetAppListResultApp;
                $app->name = htmlspecialchars_decode($rawApp['name']);
                $app->appid = $rawApp['appid'];

                $apps[] = $app;
            }
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
        return 'https://api.steampowered.com/IStoreService/GetAppList/v1/';
    }

    protected function getEssenceValue($response)
    {
        return $response['response'];
    }
}
