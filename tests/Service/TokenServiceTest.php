<?php 

namespace App\Tests\Service;

use App\Service\TokenService;
use PHPUnit\Framework\TestCase;

class TokenServiceTest extends TestCase
{
    public function testGenerateUniqueToken(): void
    {
        // Arrange
        $tokenService = new TokenService();

        // Act
        $token1 = $tokenService->generateUniqueToken();
        $token2 = $tokenService->generateUniqueToken();

        // Assert
        $this->assertNotEmpty($token1);
        $this->assertNotEmpty($token2);
        $this->assertNotEquals($token1, $token2);
    }
}
