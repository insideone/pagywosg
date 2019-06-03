<?php

namespace App\Framework\Steam\Api;

use App\Framework\Exceptions\UnexpectedResponseException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;

abstract class JsonResponseApiProvider
{
    /** @var string */
    protected $apiKey;

    /** @var Client */
    protected $client;

    /** @var SerializerInterface */
    protected $serializer;

    /** @var string */
    protected $rawResponse;

    /** @var string */
    protected $lastUrl;

    abstract protected function getUrl();

    abstract protected function getEssenceValue($response);

    protected function getDefaults()
    {
        $defauls = [];
        if ($this->isRequeresKey()) {
            $defauls['key'] = $this->apiKey;
        }
        return $defauls;
    }

    /**
     * @param array $request
     * @return mixed
     * @throws GuzzleException
     */
    protected function getResponse(array $request = [])
    {
        $request += $this->getDefaults();

        $replacements = preg_replace('~^~', '%', array_keys($request));

        $replacementValues = array_map(function ($replacement) {
            return is_array($replacement) ? implode(',', $replacement) : $replacement;
        }, $replacements);

        $baseUrl = str_replace($replacements, $replacementValues, $this->getUrl());

        $this->lastUrl = $url = $baseUrl . '?' . http_build_query($request);

        $this->rawResponse = $this->client->request(
            Request::METHOD_GET,
            $url,
            [RequestOptions::HTTP_ERRORS => false]
        )->getBody()->getContents();

        return json_decode($this->rawResponse, true);
    }

    /**
     * @param array $request
     * @return mixed
     * @throws GuzzleException
     * @throws UnexpectedResponseException
     */
    protected function getEssence(array $request = [])
    {
        $response = $this->getResponse($request);

        $essence = @$this->getEssenceValue($response);
        if (!isset($essence)) {
            throw new UnexpectedResponseException(
                "Unexpected response while fetching {$this->lastUrl}.\n".
                'First 100 symbols of response: '.substr($this->rawResponse, 0, 100)
            );
        }

        return $essence;
    }

    public function setApiKey($apiKey)
    {
        $this->apiKey = $apiKey;
        return $this;
    }

    public function setClient(Client $client)
    {
        $this->client = $client;
        return $this;
    }

    public function setSerializer(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
        return $this;
    }

    protected function isRequeresKey()
    {
        return true;
    }

    /**
     * @param array $data
     * @param string $class
     * @return object|object[]
     */
    protected function deserialize(array $data, string $class)
    {
        return $this->serializer->deserialize(
            json_encode($data),
            $class,
            'json'
        );
    }

    protected function deserializeCollection(array $data, string $class)
    {
        return $this->deserialize($data, "{$class}[]");
    }
}
