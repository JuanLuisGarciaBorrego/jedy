<?php

namespace test\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CategoryControllerTest extends WebTestCase
{
    public function testIndexAction()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/admin/categories/');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains('Categories', $crawler->filter('h1.h-btn-line')->text());
    }
}