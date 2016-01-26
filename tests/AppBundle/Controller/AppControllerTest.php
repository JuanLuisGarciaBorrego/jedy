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

}