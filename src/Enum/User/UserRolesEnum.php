<?php

namespace App\Enum\User;

class UserRolesEnum
{
    /**
     * @var string
     */
    public const ROLE_USER = 'ROLE_USER';

    /**
     * @var string[]
     */
    public const ALL = [
        self::ROLE_USER,
    ];
}
