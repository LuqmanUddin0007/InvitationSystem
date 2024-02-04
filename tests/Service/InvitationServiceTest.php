<?php 

namespace App\Tests\Service;

use App\Entity\Token;
use App\Entity\Invitation;
use App\Repository\TokenRepository;
use App\Repository\InvitationRepository;
use App\Repository\UserRepository;
use App\Service\InvitationService;
use App\Service\TokenService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;

class InvitationServiceTest extends TestCase
{
    private $entityManager;
    private $userRepository;
    private $invitationRepository;
    private $tokenRepository;
    private $tokenService;

    protected function setUp(): void
    {
        // required dependencies
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->userRepository = $this->createMock(UserRepository::class);
        $this->invitationRepository = $this->createMock(InvitationRepository::class);
        $this->tokenRepository = $this->createMock(TokenRepository::class);
        $this->tokenService = $this->createMock(TokenService::class);
    }

    public function testGenerateToken(): void
    {
        
        $senderId = 1;

        // Mock EntityManager
        $this->entityManager
            ->expects($this->once())
            ->method('persist');

        $this->entityManager
            ->expects($this->once())
            ->method('flush');

        // Mock TokenService
        $this->tokenService
            ->expects($this->once())
            ->method('generateUniqueToken')
            ->willReturn('generated_token');

        // Act
        $invitationService = new InvitationService(
            $this->entityManager,
            $this->userRepository,
            $this->invitationRepository,
            $this->tokenRepository,
            $this->tokenService
        );

        $response = $invitationService->generateToken($senderId);

        // Assert
        $this->assertInstanceOf(JsonResponse::class, $response);
        $responseData = json_decode($response->getContent(), true);
        $this->assertEquals('Token generated successfully', $responseData['message']);
        $this->assertEquals(JsonResponse::HTTP_OK, $response->getStatusCode());
    }

    public function testCancelInvitation(): void
    {
        // Arrange
        $userId = 1;
        $token = 'test_token';
        $tokenEntity = new Token();
        $tokenEntity->setUserId($userId);

        // Mock TokenRepository
        $this->tokenRepository
            ->expects($this->once())
            ->method('findOneBy')
            ->with(['token' => $token])
            ->willReturn($tokenEntity);

        // Mock EntityManager
        $this->entityManager
            ->expects($this->once())
            ->method('remove');

        $this->entityManager
            ->expects($this->once())
            ->method('flush');

        // Act
        $invitationService = new InvitationService(
            $this->entityManager,
            $this->userRepository,
            $this->invitationRepository,
            $this->tokenRepository,
            $this->tokenService
        );

        $response = $invitationService->cancelInvitation($userId, $token);

        // Assert
        $this->assertInstanceOf(JsonResponse::class, $response);
        $responseData = json_decode($response->getContent(), true);
        $this->assertEquals('Invitation cancellation processed successfully', $responseData['message']);
        $this->assertEquals(JsonResponse::HTTP_OK, $response->getStatusCode());
    }

    public function testRespondInvitation(): void
    {
        // Arrange
        $userId = 2;
        $senderId = 1;
        $token = 'test_token';
        $response = 'accepted';

        $tokenEntity = new Token();
        $tokenEntity->setUserId($senderId);

        // Mock TokenRepository
        $this->tokenRepository
            ->expects($this->once())
            ->method('findOneBy')
            ->with(['token' => $token])
            ->willReturn($tokenEntity);

        // Mock InvitationRepository
        $this->invitationRepository
            ->expects($this->once())
            ->method('findOneBy')
            ->willReturn(null);

        // Mock EntityManager
        $this->entityManager
            ->expects($this->once())
            ->method('flush');

        // Act
        $invitationService = new InvitationService(
            $this->entityManager,
            $this->userRepository,
            $this->invitationRepository,
            $this->tokenRepository,
            $this->tokenService
        );

        $response = $invitationService->respondInvitation($userId, $token, $response);

        // Assert
        $this->assertInstanceOf(JsonResponse::class, $response);
        $responseData = json_decode($response->getContent(), true);
        $this->assertEquals('Your invitation status is updated to accepted', $responseData['message']);
        $this->assertEquals(JsonResponse::HTTP_OK, $response->getStatusCode());
    }
}
