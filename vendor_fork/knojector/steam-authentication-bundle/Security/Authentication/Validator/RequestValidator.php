<?php

namespace Knojector\SteamAuthenticationBundle\Security\Authentication\Validator;

use Knojector\SteamAuthenticationBundle\Exception\InvalidOpenIdPayloadException;
use Knojector\SteamAuthenticationBundle\OpenId\PayloadValidator;
use Knojector\SteamAuthenticationBundle\OpenId\SignedPayload;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

class RequestValidator implements RequestValidatorInterface
{
    /** @var Request */
    protected $request;

    /** @var PayloadValidator */
    protected $payloadValidator;

    /** @var RouterInterface */
    protected $router;

    /** @var string */
    protected $loginRoute;

    public function __construct(PayloadValidator $payloadValidator, RouterInterface $router, string $loginRoute)
    {
        $this->payloadValidator = $payloadValidator;
        $this->router = $router;
        $this->loginRoute = $loginRoute;
    }

    public function validate(): bool
    {
        if (!$this->validateReturnTo()) {
            return false;
        }

        if (!$this->validateOpenId()) {
            return false;
        }

        return true;
    }

    public function setRequest(Request $request): RequestValidatorInterface
    {
        $this->request = $request;
        return $this;
    }

    protected function validateReturnTo(): bool
    {
        $returnTo = $this->request->query->get('openid_return_to');
        return $returnTo === $this->router->generate($this->loginRoute, [], UrlGeneratorInterface::ABSOLUTE_URL);
    }

    protected function validateOpenId(): bool
    {
        try {
            $signedPayload = SignedPayload::fromRequest($this->request);
        } catch (InvalidOpenIdPayloadException $e) {
            return false;
        }

        if (!$this->payloadValidator->validate($signedPayload)) {
            return false;
        }

        return true;
    }
}
