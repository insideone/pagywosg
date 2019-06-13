<?php

namespace Knojector\SteamAuthenticationBundle\Security\Authentication\Validator;

use Symfony\Component\HttpFoundation\Request;

interface RequestValidatorInterface
{
    public function validate(): bool;

    public function setRequest(Request $request): self;
}
