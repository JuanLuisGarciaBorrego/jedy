<?php

namespace test\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CategoryControllerTest extends WebTestCase
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    private $name = 'TestCategory';

    public function testIndexAction()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/admin/categories/');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains('Categories', $crawler->filter('h1.h-btn-line')->text());
    }

    /**
     * Creation a new Category without parent category
     */
    public function testNewAction()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/admin/category/new/');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $buttonCrawler = $crawler->selectButton('Add category')->form();

        //Error validation
        $client->submit($buttonCrawler);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertRegExp(
            '/This value should not be blank./',
            $client->getResponse()->getContent()
        );

        //Good
        $buttonCrawler['category_form[name]'] = $this->name;

        $client->submit($buttonCrawler);

        $this->assertEquals(200, $client->getResponse()->isRedirect());
        $client->followRedirect();

        $this->assertContains(
            'created_successfully',
            $client->getResponse()->getContent()
        );
    }

    /**
     * Validation HasTranslationParent category
     */
    public function testNewParentAction()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/admin/category/new/');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $buttonCrawler = $crawler->selectButton('Add category')->form();

        $idSelect = $crawler->filter('#category_form_parent option')->last()->attr('value');

        $buttonCrawler['category_form[name]'] = 'SubCategory of '.$this->name;
        $buttonCrawler['category_form[parent]'] = $idSelect;

        $client->submit($buttonCrawler);

        $this->assertRegExp(
            '/You must create the translations of the parent category/',
            $client->getResponse()->getContent()
        );
    }

    /**
     * Count translations of a category
     */
    public function testTranslationsAction()
    {
        $client = static::createClient();

        $route = "/admin/category/".$this->selectCategoryByName()->getId()."/translations/";
        $client->request('GET', $route);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

    }

    /**
     * @return mixed
     */
    private function selectCategoryByName()
    {
        self::bootKernel();

        $this->em = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        return $this->em->getRepository('AppBundle:Category')->findOneBy(['name' => $this->name]);
    }
}