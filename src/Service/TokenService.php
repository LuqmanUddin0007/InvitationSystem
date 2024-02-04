<?php

namespace App\Service;

/**
 * Class TokenService
 * @package App\Service
 */
class TokenService
{
    /**
     * Generates a unique token.
     *
     * @return string
     */
    public function generateUniqueToken(): string
    {
        // Implement your logic to generate a unique token
         $randomBytes = random_bytes(16);
        $currentTime = new \DateTime();
        return bin2hex($randomBytes . $currentTime->format('YmdHis'));
    }
}
