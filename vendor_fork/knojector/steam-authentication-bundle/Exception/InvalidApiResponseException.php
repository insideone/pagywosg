<?php

namespace Knojector\SteamAuthenticationBundle\Exception;

/**
 * @author Knojector <dev@404-labs.xyz>
 */
class InvalidApiResponseException extends \Exception
{
    /**
     * @param string $message
     */
    public function __construct(string $message)
    {
        parent::__construct($message, 500);
    }
}