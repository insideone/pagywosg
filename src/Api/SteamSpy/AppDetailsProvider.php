<?php

namespace App\Api\SteamSpy;


use App\Api\SteamSpy\Schema\AppDetails;
use App\Framework\SteamSpy\Api\ApiProvider;
use Exception;

class AppDetailsProvider extends ApiProvider
{

    /**
     * @param $steamGameId
     * @return AppDetails
     * @throws Exception
     */
    public function getDetails($steamGameId)
    {
        $response = $this->getResponse('appdetails', ['appid' => $steamGameId]);

        /** @var AppDetails $appDetails */
        $appDetails = $this->serializer->deserialize(
            $response,
            AppDetails::class,
            'json'
        );

        if ($appDetails->id === 999999)
            throw new Exception('SteamSpy data for this application is hidden on developer\'s request.');

        if (!$appDetails->name)
            throw new Exception('SteamSpy doesn\'t have information about this app.');

        return $appDetails;

    }
}