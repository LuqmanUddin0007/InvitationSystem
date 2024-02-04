<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class InvitationControllerTest extends WebTestCase
{
    public function testSendInvitation()
    {
        $client = static::createClient();

        $client->request(
            'POST',
            '/invite',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(['token' => 'your_token', 'user_id' => 123])
        );

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertJson($client->getResponse()->getContent());
    }

    public function testCancelInvitation()
    {
        $client = static::createClient();

        $client->request(
            'POST',
            '/cancel/123/your_token',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json']
        );

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertJson($client->getResponse()->getContent());
    }

    public function testRespondInvitation()
    {
        $client = static::createClient();

        $client->request(
            'POST',
            '/respond/123/your_token/accept',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json']
        );

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertJson($client->getResponse()->getContent());
    }
}
