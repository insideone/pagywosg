<?php

namespace Knojector\SteamAuthenticationBundle\Exception;

/**
 * @author Knojector <dev@404-labs.xyz>
 */
class InvalidUserClassException extends \Exception
{
    /**
     * @param string $className
     */
    public function __construct(string $className)
    {
        parent::__construct(
            sprintf('The class "%s" can not be used for Steam authentication.', $className),
            500
        );
    }
}