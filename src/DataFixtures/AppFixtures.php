<?php
namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Token;
use App\Entity\Invitation;
use App\Service\TokenService; // Import TokenService if not already imported
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    private $tokenService;

    public function __construct(TokenService $tokenService)
    {
        $this->tokenService = $tokenService;
    }

    public function load(ObjectManager $manager)
    {
        // Load User data
        for ($i = 1; $i <= 10; $i++) {
            $user = new User();
            $user->setUsername("user{$i}");
            $manager->persist($user);
        }

        $manager->flush();
    }
}
