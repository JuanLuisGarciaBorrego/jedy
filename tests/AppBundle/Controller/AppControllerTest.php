<?php

namespace Test\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AppControllerTest extends WebTestCase
{
    public function testIndexAction()
    {
        $client = static::createClient();

        $client->request('GET', '/');

        $this->assertEquals(200, $client->getResponse()->isRedirect());
        $client->followRedirect();

        $this->assertContains(
            'Jedy CMS',
            $client->getResponse()->getContent()
        );
    }

    public function testPageNotFound()
    {
        $client = static::createClient();

        $client->request('GET', '/en/page-not-found');

        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }

    public function testTryIntoAdminZone()
    {
        $client = static::createClient();

        $client->request('GET', '/en/admin');

        $this->assertEquals(401, $client->getResponse()->getStatusCode());
    }
}