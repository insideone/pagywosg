<?php

namespace App\Framework\SteamSpy\Api;

use Goutte\Client as GoutteClient;
use Symfony\Component\Serializer\SerializerInterface;

class ApiProvider
{
    const BASE_API_URL = 'https://steamspy.com/api.php';

    /** @var GoutteClient  */
    private $goutteClient;

    /** @var SerializerInterface */
    protected $serializer;

    public function __construct(GoutteClient $goutteClient, SerializerInterface $serializer)
    {
        $this->goutteClient = $goutteClient;
        $this->serializer = $serializer;
    }

    protected function getResponse($method, $params)
    {
        $url = $this::BASE_API_URL.'?'.http_build_query(['request' => $method] + $params);

        $this->goutteClient->request('GET', $url);
        $response = $this->goutteClient->getResponse()->getContent();

        return $response;
    }


}