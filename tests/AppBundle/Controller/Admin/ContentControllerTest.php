<?php

namespace Test\AppBundle\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Test\AppBundle\Controller\CategoryControllerTest;

class ContentControllerTest extends WebTestCase
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    private $nameCategory = 'TestCategoryA';

    private $nameTitle = 'Title test';

    private $content = 'Lo ren Impsun Loren Impsun Loren Impsun Loren';

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
        $crawler = $this->client->request('GET', '/en/admin/contents');

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertContains('Contents', $crawler->filter('h1.h-btn-line')->text());
    }

    /**
     * Creation a Page
     */
    public function testNewPageAction()
    {
        $crawler = $this->client->request('GET', '/en/admin/content/page/new/');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $buttonCrawler = $crawler->selectButton('Add content - Page')->form();

        //Error validation
        $this->client->submit($buttonCrawler);

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertRegExp(
            '/This value should not be blank./',
            $this->client->getResponse()->getContent()
        );

        //Good
        $buttonCrawler['content_form[title]'] = $this->nameTitle."Page";
        $buttonCrawler['content_form[content]'] = $this->content;

        $this->client->submit($buttonCrawler);

        $this->assertEquals(200, $this->client->getResponse()->isRedirect());
        $this->client->followRedirect();

        $this->assertContains(
            'The content was created',
            $this->client->getResponse()->getContent()
        );
    }

    /**
     * Creation a Translation Page En
     */
    public function testTranslationPageEnAction()
    {
        $routeEn = "en/admin/content/".$this->selectContentByTitle($this->nameTitle."Page")->getId(
            )."/translations/add/es/en";
        $crawler = $this->client->request('GET', $routeEn);
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $buttonCrawler = $crawler->selectButton('Add content - Page')->form();
        $buttonCrawler['content_form[title]'] = $this->nameTitle."Page En";
        $buttonCrawler['content_form[content]'] = "En-".$this->content;

        $this->client->submit($buttonCrawler);

        $this->assertEquals(200, $this->client->getResponse()->isRedirect());
        $this->client->followRedirect();

        $this->assertContains(
            'Translation content was created',
            $this->client->getResponse()->getContent()
        );
    }

    /**
     * Creation a Translation Page Fr
     */
    public function testTranslationPageFrAction()
    {
        $routeFr = "en/admin/content/".$this->selectContentByTitle($this->nameTitle."Page")->getId(
            )."/translations/add/es/fr";
        $crawler = $this->client->request('GET', $routeFr);
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $buttonCrawler = $crawler->selectButton('Add content - Page')->form();
        $buttonCrawler['content_form[title]'] = $this->nameTitle."Page Fr";
        $buttonCrawler['content_form[content]'] = "Fr-".$this->content;

        $this->client->submit($buttonCrawler);

        $this->assertEquals(200, $this->client->getResponse()->isRedirect());
        $this->client->followRedirect();

        $this->assertContains(
            'Translation content was created',
            $this->client->getResponse()->getContent()
        );
    }

    /**
     * Edit a Translation Page Fr
     */
    public function testEditTranslationPageFrAction()
    {
        $routeFr = "en/admin/content/".$this->selectContentByTitle($this->nameTitle."Page")->getId(
            )."/translations/".$this->selectContentByTitle($this->nameTitle."Page Fr")->getId()."/edit/es/fr";
        $crawler = $this->client->request('GET', $routeFr);
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $buttonCrawler = $crawler->selectButton('Edit content - Page')->form();
        $buttonCrawler['content_form[title]'] = $this->nameTitle."Page Fr Edit";

        $this->client->submit($buttonCrawler);

        $this->assertEquals(200, $this->client->getResponse()->isRedirect());
        $this->client->followRedirect();

        $this->assertContains(
            'Translation content was edited',
            $this->client->getResponse()->getContent()
        );
    }

    /**
     * Delete Page Fr
     */
    public function testDeleteTranslationPage()
    {
        $routeFr = "en/admin/content/".$this->selectContentByTitle($this->nameTitle."Page")->getId(
            )."/translations/".$this->selectContentByTitle($this->nameTitle."Page Fr Edit")->getId()."/edit/es/fr";
        $crawler = $this->client->request('GET', $routeFr);

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $buttonCrawler = $crawler->selectButton('Delete')->form();
        $this->client->submit($buttonCrawler);

        $this->assertEquals(200, $this->client->getResponse()->isRedirect());
        $this->client->followRedirect();

        $this->assertContains(
            'The content was deleted',
            $this->client->getResponse()->getContent()
        );
    }

    /**
     * Delete Parent Page
     */
    public function testDeletePage()
    {
        $route = "en/admin/content/".$this->selectContentByTitle($this->nameTitle."Page")->getId()."/edit/";
        $crawler = $this->client->request('GET', $route);

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $buttonCrawler = $crawler->selectButton('Delete')->form();
        $this->client->submit($buttonCrawler);

        $this->assertEquals(200, $this->client->getResponse()->isRedirect());
        $this->client->followRedirect();

        $this->assertContains(
            'The content was deleted',
            $this->client->getResponse()->getContent()
        );
    }

    /**
     * Creation a Post
     */
    public function testNewPostAction()
    {
        $crawler = $this->client->request('GET', 'en/admin/content/post/new/');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $buttonCrawler = $crawler->selectButton('Add content - Post')->form();

        $buttonCrawler['content_form[title]'] = $this->nameTitle."Post";
        $buttonCrawler['content_form[content]'] = $this->content;

        $idSelect = $crawler->filter('#content_form_category option')->last()->attr('value');
        $buttonCrawler['content_form[category]'] = $idSelect;

        $this->client->submit($buttonCrawler);

        $this->assertEquals(200, $this->client->getResponse()->isRedirect());
        $this->client->followRedirect();

        $this->assertContains(
            'The content was created',
            $this->client->getResponse()->getContent()
        );
    }

    /**
     * Creation a Translation Post En
     */
    public function testTranslationPostEnAction()
    {
        $routeEn = "en/admin/content/".$this->selectContentByTitle($this->nameTitle."Post")->getId(
            )."/translations/add/es/en";
        $crawler = $this->client->request('GET', $routeEn);
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $buttonCrawler = $crawler->selectButton('Add content - Post')->form();
        $buttonCrawler['content_form[title]'] = $this->nameTitle."Post En";
        $buttonCrawler['content_form[content]'] = $this->content;

        $this->client->submit($buttonCrawler);

        $this->assertEquals(200, $this->client->getResponse()->isRedirect());
        $this->client->followRedirect();

        $this->assertContains(
            'Translation content was created',
            $this->client->getResponse()->getContent()
        );
    }

    /**
     * Creation a Translation Page Fr
     */
    public function testTranslationPostFrAction()
    {
        $routeFr = "en/admin/content/".$this->selectContentByTitle($this->nameTitle."Post")->getId(
            )."/translations/add/es/fr";
        $crawler = $this->client->request('GET', $routeFr);
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $buttonCrawler = $crawler->selectButton('Add content - Post')->form();
        $buttonCrawler['content_form[title]'] = $this->nameTitle."Post Fr";
        $buttonCrawler['content_form[content]'] = $this->content;

        $this->client->submit($buttonCrawler);

        $this->assertEquals(200, $this->client->getResponse()->isRedirect());
        $this->client->followRedirect();

        $this->assertContains(
            'Translation content was created',
            $this->client->getResponse()->getContent()
        );
    }

    /**
     * Delete Post Fr
     */
    public function testDeleteTranslationPost()
    {
        $routeFr = "en/admin/content/".$this->selectContentByTitle($this->nameTitle."Post")->getId(
            )."/translations/".$this->selectContentByTitle($this->nameTitle."Post Fr")->getId()."/edit/es/fr";
        $crawler = $this->client->request('GET', $routeFr);

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $buttonCrawler = $crawler->selectButton('Delete')->form();
        $this->client->submit($buttonCrawler);

        $this->assertEquals(200, $this->client->getResponse()->isRedirect());
        $this->client->followRedirect();

        $this->assertContains(
            'The content was deleted',
            $this->client->getResponse()->getContent()
        );
    }

    /**
     * Delete Post
     */
    public function testDeletePost()
    {
        $route = "en/admin/content/".$this->selectContentByTitle($this->nameTitle."Post")->getId()."/edit/";
        $crawler = $this->client->request('GET', $route);

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $buttonCrawler = $crawler->selectButton('Delete')->form();
        $this->client->submit($buttonCrawler);

        $this->assertEquals(200, $this->client->getResponse()->isRedirect());
        $this->client->followRedirect();

        $this->assertContains(
            'The content was deleted',
            $this->client->getResponse()->getContent()
        );
    }

    public function testDestructInitialCategory()
    {
        $route = "/en/admin/category/".$this->selectCategoryByName($this->nameCategory)->getId()."/edit/";
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

    /**
     * @param $title
     * @return mixed
     */
    private function selectContentByTitle($title)
    {
        self::bootKernel();

        $this->em = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        return $this->em->getRepository('AppBundle:Content')->findOneBy(['title' => $title]);
    }
}