<?php

namespace App\Api\Steam;

use App\Api\Steam\Schema\AppDetails;
use App\Framework\Exceptions\UnexpectedResponseException;
use App\Framework\Steam\Api\JsonResponseApiProvider;
use GuzzleHttp\Exception\GuzzleException;
use InvalidArgumentException;

class AppDetailsInnerProvider extends JsonResponseApiProvider
{
    protected $appId;

    /**
     * @param int $appId
     * @return AppDetails|null
     * @throws UnexpectedResponseException
     * @throws GuzzleException
     */
    public function fetch(int $appId)
    {
        if ($appId <= 0) {
            throw new InvalidArgumentException('appId must be positive integer');
        }

        $this->appId = $appId;

        $appDetails = $this->serializer->deserialize(
            json_encode($this->getEssence(['appids' => $appId])),
            AppDetails::class,
            'json'
        );

        return $appDetails instanceof AppDetails ? $appDetails : null;
    }

    protected function getUrl()
    {
        return 'https://store.steampowered.com/api/appdetails';
    }

    protected function getEssenceValue($response)
    {
        return $response[$this->appId]['data'];
    }
}
