<?php

namespace Knojector\SteamAuthenticationBundle\OpenId;

use GuzzleHttp\Client;

class PayloadValidator
{
    const URL = 'https://steamcommunity.com/openid/login';

    /**
     * @var Client
     */
    private $client;

    public function __construct()
    {
        $this->client = new Client();
    }

    public function validate(SignedPayload $payload)
    {
        $params = $payload->toArray([
            'mode' => 'check_authentication',
        ]);

        $response = $this->client->post(self::URL, [
            'form_params' => $params
        ])->getBody()->getContents();

        return preg_match('~is_valid\s*:\s*true~i', $response) === 1;
    }
}
