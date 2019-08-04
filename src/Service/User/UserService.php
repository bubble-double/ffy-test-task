<?php

namespace App\Service\User;

use App\Entity\User;

class UserService
{
    /**
     * @param User $user
     *
     * @return array
     */
    public function getPublicData(User $user): array
    {
        return [
            'id' => $user->getId(),
            'username' => $user->getUsername(),
            'lastName' => $user->getLastName(),
            'email' => $user->getEmail(),
        ];
    }
}
