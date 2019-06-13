<?php

namespace App\Security;

use Knojector\SteamAuthenticationBundle\Security\Authentication\Validator\RequestValidator;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class OpenIdRequestValidator extends RequestValidator
{
    protected function validateReturnTo(): bool
    {
        $requestedReturnTo = $this->request->query->get('openid_return_to');
        $knownReturnTo = $this->router->generate($this->loginRoute, [], UrlGeneratorInterface::ABSOLUTE_URL);

        return $this->getUrlWithoutSchema($requestedReturnTo) === $this->getUrlWithoutSchema($knownReturnTo);
    }

    protected function getUrlWithoutSchema($url)
    {
        return preg_replace('~^(http[s]?)~', '', $url);
    }
}
