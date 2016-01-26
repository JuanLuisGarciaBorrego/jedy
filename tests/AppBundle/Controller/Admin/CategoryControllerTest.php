<?php

namespace Test\AppBundle\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CategoryControllerTest extends WebTestCase
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    private $name = 'TestCategory';

    private $client;

    protected function setUp()
    {
        $this->client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'jedy',
            'PHP_AUTH_PW'   => '1234',
        ));
    }

    public function testIndexAction()
    {
        $crawler = $this->client->request('GET', '/en/admin/categories/');

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertContains('Categories', $crawler->filter('h1.h-btn-line')->text());
    }

    /**
     * Creation a new Category without parent category
     */
    public function testNewAction()
    {
        $crawler = $this->client->request('GET', '/en/admin/category/new/');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $buttonCrawler = $crawler->selectButton('Add category')->form();

        //Error validation
        $this->client->submit($buttonCrawler);

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertRegExp(
            '/This value should not be blank./',
            $this->client->getResponse()->getContent()
        );

        //Good
        $buttonCrawler['category_form[name]'] = $this->name;

        $this->client->submit($buttonCrawler);

        $this->assertEquals(200, $this->client->getResponse()->isRedirect());
        $this->client->followRedirect();

        $this->assertContains(
            'The category was created',
            $this->client->getResponse()->getContent()
        );
    }

    /**
     * Validation HasTranslationParent category error
     */
    public function testNewParentErrorAction()
    {
        $crawler = $this->client->request('GET', '/en/admin/category/new/');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $buttonCrawler = $crawler->selectButton('Add category')->form();

        $idSelect = $crawler->filter('#category_form_parent option')->last()->attr('value');

        $buttonCrawler['category_form[name]'] = 'SubCategory of '.$this->name;
        $buttonCrawler['category_form[parent]'] = $idSelect;

        $this->client->submit($buttonCrawler);

        $this->assertRegExp(
            '/You must create the translations of the parent category/',
            $this->client->getResponse()->getContent()
        );
    }

    /**
     * Count translations of a category
     */
    public function testTranslationsAction()
    {
        $route = "/en/admin/category/".$this->selectCategoryByName($this->name)->getId()."/translations/";
        $this->client->request('GET', $route);
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

    }

    /**
     * add translation to category in English
     */
    public function testAddTranslationEnAction()
    {
        $routeEn = "en/admin/category/".$this->selectCategoryByName($this->name)->getId()."/translations/add/es/en";

        //English
        $crawler = $this->client->request('GET', $routeEn);
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $buttonCrawler = $crawler->selectButton('Add translation to category')->form();

        $buttonCrawler['category_form[name]'] = $this->name."En";

        $this->client->submit($buttonCrawler);

        $this->assertEquals(200, $this->client->getResponse()->isRedirect());
        $this->client->followRedirect();

        $this->assertContains(
            'Translation category was created',
            $this->client->getResponse()->getContent()
        );
    }

    /**
     * add translation to category in French
     */
    public function testAddTranslationFrAction()
    {
        $routeFr = "en/admin/category/".$this->selectCategoryByName($this->name)->getId()."/translations/add/es/fr";

        //French
        $crawler = $this->client->request('GET', $routeFr);
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $buttonCrawler = $crawler->selectButton('Add translation to category')->form();

        $buttonCrawler['category_form[name]'] = $this->name."Fr";

        $this->client->submit($buttonCrawler);

        $this->assertEquals(200, $this->client->getResponse()->isRedirect());
        $this->client->followRedirect();

        $this->assertContains(
            'Translation category was created',
            $this->client->getResponse()->getContent()
        );
    }

    /**
     * Create a translation parent category ok
     */
    public function testNewParentOkAction()
    {
        $crawler = $this->client->request('GET', '/en/admin/category/new/');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $buttonCrawler = $crawler->selectButton('Add category')->form();

        $idSelect = $crawler->filter('#category_form_parent option')->last()->attr('value');

        $buttonCrawler['category_form[name]'] = 'SubCategory of '.$this->name;
        $buttonCrawler['category_form[parent]'] = $idSelect;

        $this->client->submit($buttonCrawler);

        $this->assertEquals(200, $this->client->getResponse()->isRedirect());
        $this->client->followRedirect();

        $this->assertContains(
            'The category was created',
            $this->client->getResponse()->getContent()
        );
    }

    /**
     * Edit translation english
     */
    public function testEditTranslationAction()
    {
        $route = "/en/admin/category/".$this->selectCategoryByName($this->name)->getId(
            )."/translations/".$this->selectCategoryByName($this->name."En")->getId()."/edit/es/en";

        $crawler = $this->client->request('GET', $route);
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $buttonCrawler = $crawler->selectButton('Edit category')->form();

        $buttonCrawler['category_form[name]'] = "Edit En";
        $this->client->submit($buttonCrawler);

        $this->assertEquals(200, $this->client->getResponse()->isRedirect());
        $this->client->followRedirect();

        $this->assertContains(
            'Translation category was edited',
            $this->client->getResponse()->getContent()
        );
    }

    /**
     * Test Edit Category
     */
    public function testEditAction()
    {
        $route = "/en/admin/category/".$this->selectCategoryByName($this->name)->getId()."/edit/";
        $crawler = $this->client->request('GET', $route);

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $buttonCrawler = $crawler->selectButton('Edit category')->form();
        $buttonCrawler['category_form[name]'] = $this->name."A";

        $this->client->submit($buttonCrawler);

        $this->assertEquals(200, $this->client->getResponse()->isRedirect());
        $this->client->followRedirect();

        $this->assertContains(
            'The category was edited',
            $this->client->getResponse()->getContent()
        );
    }

    public function testDeleteSubCategory()
    {
        $route = "/en/admin/category/".$this->selectCategoryByName("SubCategory of ".$this->name)->getId()."/edit/";
        $crawler = $this->client->request('GET', $route);

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $buttonCrawler = $crawler->selectButton('Delete')->form();
        $this->client->submit($buttonCrawler);

        $this->assertEquals(200, $this->client->getResponse()->isRedirect());
        $this->client->followRedirect();

        $this->assertContains(
            'The category was deleted',
            $this->client->getResponse()->getContent()
        );
    }

    /**
     * @return mixed
     */
    private function selectCategoryByName($name)
    {
        self::bootKernel();

        $this->em = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        return $this->em->getRepository('AppBundle:Category')->findOneBy(['name' => $name]);
    }
}