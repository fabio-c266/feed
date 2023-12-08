<?php

namespace src\Core;

use Ramsey\Uuid\Uuid as libUUID;

class UUID
{
    public static function generate()
    {
        $uuid = libUUID::uuid4();
        return $uuid->toString();
    }
}
