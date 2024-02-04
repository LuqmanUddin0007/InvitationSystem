<?php

namespace App\Service;

use App\Entity\Invitation;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\InvitationRepository;
use App\Repository\UserRepository;
use App\Entity\Token;
use App\Repository\TokenRepository;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class InvitationService
 * @package App\Service
 */
class InvitationService implements InvitationServiceInterface
{
    /**
     * InvitationService constructor
     *
     * @param EntityManagerInterface $entityManager
     * @param UserRepository $userRepository
     * @param InvitationRepository $invitationRepository
     */
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserRepository $userRepository,
        private InvitationRepository $invitationRepository,
        private TokenRepository $tokenRepository,
        private TokenService $tokenService
    ) {
    }

    /**
     * Sends an invitation from the sender to the receiver.
     *
     * @param integer $senderId
     * @return JsonResponse|null
     */
    public function generateToken(int $senderId): ?JsonResponse
    {
        try {
            $token = new Token();
            $token->setUserId($senderId);
            $token->setToken($this->tokenService->generateUniqueToken());
            $this->entityManager->persist($token);
            $this->entityManager->flush();

            return new JsonResponse(['message' => 'Token generated successfully'], JsonResponse::HTTP_OK);

        } catch (\Exception $exception) {
            return new JsonResponse(['error' => 'Unable to generate token. ' . $exception->getMessage()], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Cancels a sent invitation.
     *
     * @param int $userId
     * @param string $token
     * @return JsonResponse|null
     */
    public function cancelInvitation(int $userId, string $token): ?JsonResponse
    {
        $tokenEntity = $this->tokenRepository->findOneBy(['token' => $token]);

        if ($tokenEntity) {
            $dbUserId = $tokenEntity->getUserId();

            if ($dbUserId == $userId) {
                $this->entityManager->remove($tokenEntity);
                $this->entityManager->flush();

                return new JsonResponse(['message' => 'Invitation cancellation processed successfully'], JsonResponse::HTTP_OK);
            } else {
                return new JsonResponse(['error' => 'Unauthorized operation'], JsonResponse::HTTP_UNAUTHORIZED);
            }
        } else {
            return new JsonResponse(['error' => 'Invitation not found'], JsonResponse::HTTP_NOT_FOUND);
        }
    }

    /**
     * Responds to an invitation with either acceptance or decline.
     *
     * @param int $userId
     * @param string $token
     * @param string $response
     * @return JsonResponse|null
     */
    public function respondInvitation(int $userId, string $token, string $response): ?JsonResponse
    {
        $tokenEntity = $this->tokenRepository->findOneBy(['token' => $token]);

        if ($tokenEntity) {
            $senderId = $tokenEntity->getUserId();

            if ($userId !== $senderId) {
                $tokenId = $tokenEntity->getId();

                $consumedInvitation = $this->invitationRepository->findOneBy(['userId' => $userId, 'token_id' => $tokenId]);

                if ($consumedInvitation) {
                    return new JsonResponse(['error' => 'You have already responded to the invitation'], JsonResponse::HTTP_BAD_REQUEST);
                } else {
                    if ($response === Invitation::STATUS_ACCEPTED || $response === Invitation::STATUS_DECLINED) {
                        $invitation = new Invitation();
                        $invitation->setStatus($response);

                        $this->entityManager->flush();

                        return new JsonResponse(['message' => 'Your invitation status is updated to ' . $response], JsonResponse::HTTP_OK);
                    } else {
                        return new JsonResponse(['error' => 'Invalid status request'], JsonResponse::HTTP_BAD_REQUEST);
                    }
                }
            } else {
                return new JsonResponse(['error' => 'Sender cannot accept or decline their own invitation'], JsonResponse::HTTP_BAD_REQUEST);
            }
        } else {
            return new JsonResponse(['error' => 'Invitation not found'], JsonResponse::HTTP_NOT_FOUND);
        }
    }
}
