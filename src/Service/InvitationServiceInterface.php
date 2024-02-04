<?php

namespace App\Service;
use App\Entity\Token;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Interface InvitationServiceInterface
 * @package App\Service
 */
interface InvitationServiceInterface
{
   /**
     * 
     *
     * @param string $token
     * @param int $receiverId
     * @return Invitation|null
     * @throws \Exception
     */

     /**
      * generate token by sender.
      * @param integer $senderId
      * @return JsonResponse|null
      */
    public function generateToken(int $senderId): ?JsonResponse;

    /**
     * Cancels a previously sent invitation.
     *
     * @param integer $invitation
     * @return void
    */
    public function cancelInvitation(int $userId, string $token): ?JsonResponse;

    /**
     * Responds to an invitation with either acceptance or decline.
     *
     * @param integer $invitation
     * @param string $response
     * @return void
     */
    public function respondInvitation(int $userId, string $token, string $response): ?JsonResponse;
}
