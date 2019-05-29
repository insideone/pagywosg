<?php

namespace App\DBAL\Types;

use Fresh\DoctrineEnumBundle\DBAL\Types\AbstractEnumType;

class PlayStatusType extends AbstractEnumType
{
    public const UNFINISHED = 'unfinished';
    public const BEATEN = 'beaten';
    public const COMPLETED = 'completed';

    protected static $choices = [
        self::UNFINISHED => 'Unfinished',
        self::BEATEN => 'Beaten',
        self::COMPLETED => 'Completed',
    ];
}
