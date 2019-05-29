<?php

namespace App\Framework\Entity;

use DateTime;
use Exception;

trait CommonEntityTrait
{
    private function getNormalizedDateTime($dateTime) : ?DateTime
    {
        if (!$dateTime) {
            return null;
        }

        if (is_string($dateTime)) {
            try {
                return new DateTime($dateTime);
            } catch (Exception $e) {
                return null;
            }
        } elseif ($dateTime instanceof DateTime) {
            return $dateTime;
        }

        return null;
    }
}
