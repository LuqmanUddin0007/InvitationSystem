<?php

namespace App\Controller;

use App\Service\InvitationServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Controller for handling invitation-related actions.
 *
 * @package App\Controller
 */
class InvitationController extends AbstractController
{
    /**
     * InvitationController constructor.
     * @param InvitationServiceInterface $invitationService
     */
    public function __construct(private InvitationServiceInterface $invitationService)
    {
        $this->invitationService = $invitationService;
    }

    /**
     * generate an invitation and store in the token table.
     *
     * @param Request $request
     */
    #[Route('/generateToken', methods: ['POST'])]
    public function sendInvitation(Request $request)
    {
        $requestData = json_decode($request->getContent(), true);
        $userId = $requestData['user_id'];

        try {
            // Generate an invitation token
            return $this->invitationService->generateToken($userId);
        } catch (\Exception $e) {
            // Handle exceptions and return an error response
            return $this->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Cancels a previously sent invitation.
     *
     * @param int    $userId
     * @param string $token
     */
    #[Route('/cancelInvitation/{userId}/{token}', methods: ['POST'])]
    public function cancelInvitation(int $userId, string $token)
    {
        // Cancel the invitation and return a response
        return $this->invitationService->cancelInvitation($userId, $token);
    }

    /**
     * Responds to an invitation with either acceptance or decline.
     *
     * @param int    $userId
     * @param string $token
     * @param string $response
     */
    #[Route('/respondToInvitation/{userId}/{token}', methods: ['POST'])]
    public function respondInvitation(int $userId, string $token, string $response)
    {
        // Respond to the invitation and return a response
        return $this->invitationService->respondInvitation($userId, $token, $response);
    }
}