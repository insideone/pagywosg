<?php

namespace Knojector\SteamAuthenticationBundle\OpenId;

use Knojector\SteamAuthenticationBundle\Exception\InvalidOpenIdPayloadException;
use Symfony\Component\HttpFoundation\Request;

class SignedPayload
{
    const DEFAULT_PREFIX = 'openid_';

    /** @var string */
    protected $signature;

    /** @var array */
    protected $data;

    /**
     * SignedPayload constructor.
     * @param string $signature
     * @param string[] $composition
     * @param array $data
     * @throws InvalidOpenIdPayloadException
     */
    public function __construct(string $signature, array $composition, array $data)
    {
        if (empty($signature)) {
            throw new InvalidOpenIdPayloadException('Empty signature');
        }

        if (!$composition) {
            throw new InvalidOpenIdPayloadException('Empty composition');
        }

        if (array_diff_key(array_flip($composition), $data)) {
            throw new InvalidOpenIdPayloadException('Incomplete data');
        }

        $this->signature = $signature;
        $this->data = $data;
    }

    /**
     * @param Request $request
     * @param string $prefix
     * @return self
     * @throws InvalidOpenIdPayloadException
     */
    public static function fromRequest(Request $request, string $prefix = self::DEFAULT_PREFIX)
    {
        $fields = [];
        foreach ($request->query as $key => $value) {
            if (!preg_match('~^'.preg_quote($prefix).'(.+)~', $key, $m)) {
                continue;
            }

            [, $name] = $m;
            $fields[$name] = $value;
        }

        $requiredFields = ['sig', 'signed'];
        foreach ($requiredFields as $requiredField) {
            if (empty($fields[$requiredField])) {
                throw new InvalidOpenIdPayloadException("`{$requiredField}` is missed");
            }
        }

        return new self($fields['sig'], explode(',', $fields['signed']), $fields);
    }

    public function toArray(array $data = [], string $prefix = self::DEFAULT_PREFIX)
    {
        $data += $this->data;

        return array_combine(
            preg_replace('~^~', $prefix, array_keys($data)),
            $data
        );
    }
}
