<?php

namespace Test\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Test\AppBundle\Controller\CategoryControllerTest;

class ContentControllerTest extends WebTestCase
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    private $nameCategory = 'DefaultCategory';

    private $nameTitle = 'Title test';

    private $content = 'Lo ren Impsun Loren Impsun Loren Impsun LorenImpsun, Loren Impsun Loren Impsun Lo ren Imp sun. LorenImp sun Loren Impsun, LorenImp sun Loren Impsun LorenImp sun.';

    /**
     * Init Category Es,En,Fr
     */
    public function testInitialCategoryAction()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/en/admin/category/new/');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $buttonCrawler = $crawler->selectButton('Add category')->form();

        $buttonCrawler['category_form[name]'] = $this->nameCategory;
        $client->submit($buttonCrawler);

        //English
        $client = static::createClient();
        $routeEn = "en/admin/category/".$this->selectCategoryByName($this->nameCategory)->getId(
            )."/translations/add/es/en";

        $crawler = $client->request('GET', $routeEn);
        $buttonCrawler = $crawler->selectButton('Add translation category')->form();

        $buttonCrawler['category_form[name]'] = $this->nameCategory."En";

        $client->submit($buttonCrawler);

        //French
        $client = static::createClient();
        $routeFr = "en/admin/category/".$this->selectCategoryByName($this->nameCategory)->getId(
            )."/translations/add/es/fr";
        $crawler = $client->request('GET', $routeFr);
        $buttonCrawler = $crawler->selectButton('Add translation category')->form();
        $buttonCrawler['category_form[name]'] = $this->nameCategory."Fr";

        $client->submit($buttonCrawler);
    }

    public function testIndexAction()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/en/admin/contents');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains('Contents', $crawler->filter('h1.h-btn-line')->text());
    }

    /**
     * Creation a Page
     */
    public function testNewPageAction()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', 'en/admin/content/page/new/');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $buttonCrawler = $crawler->selectButton('Add content - page')->form();

        //Error validation
        $client->submit($buttonCrawler);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertRegExp(
            '/This value should not be blank./',
            $client->getResponse()->getContent()
        );

        //Good
        $buttonCrawler['content_form[title]'] = $this->nameTitle."Page";
        $buttonCrawler['content_form[content]'] = $this->content;

        $client->submit($buttonCrawler);

        $this->assertEquals(200, $client->getResponse()->isRedirect());
        $client->followRedirect();

        $this->assertContains(
            'created_successfully',
            $client->getResponse()->getContent()
        );
    }

    /**
     * Creation a Translation Page En
     */
    public function testTranslationPageEnAction()
    {
        $client = static::createClient();

        $routeEn = "en/admin/content/".$this->selectContentByTitle($this->nameTitle."Page")->getId(
            )."/translations/add/es/en";
        $crawler = $client->request('GET', $routeEn);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $buttonCrawler = $crawler->selectButton('Add content - page')->form();
        $buttonCrawler['content_form[title]'] = $this->nameTitle."Page En";
        $buttonCrawler['content_form[content]'] = $this->content;

        $client->submit($buttonCrawler);

        $this->assertEquals(200, $client->getResponse()->isRedirect());
        $client->followRedirect();

        $this->assertContains(
            'created_successfully',
            $client->getResponse()->getContent()
        );
    }

    /**
     * Creation a Translation Page Fr
     */
    public function testTranslationPageFrAction()
    {
        $client = static::createClient();

        $routeFr = "en/admin/content/".$this->selectContentByTitle($this->nameTitle."Page")->getId(
            )."/translations/add/es/fr";
        $crawler = $client->request('GET', $routeFr);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $buttonCrawler = $crawler->selectButton('Add content - page')->form();
        $buttonCrawler['content_form[title]'] = $this->nameTitle."Page Fr";
        $buttonCrawler['content_form[content]'] = $this->content;

        $client->submit($buttonCrawler);

        $this->assertEquals(200, $client->getResponse()->isRedirect());
        $client->followRedirect();

        $this->assertContains(
            'created_successfully',
            $client->getResponse()->getContent()
        );
    }

    /**
     * Edit a Translation Page Fr
     */
    public function testEditTranslationPageFrAction()
    {
        $client = static::createClient();

        $routeFr = "en/admin/content/".$this->selectContentByTitle($this->nameTitle."Page")->getId(
            )."/translations/".$this->selectContentByTitle($this->nameTitle."Page Fr")->getId()."/edit/es/fr";
        $crawler = $client->request('GET', $routeFr);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $buttonCrawler = $crawler->selectButton('Edit content - page')->form();
        $buttonCrawler['content_form[title]'] = $this->nameTitle."Page Fr Edit";

        $client->submit($buttonCrawler);

        $this->assertEquals(200, $client->getResponse()->isRedirect());
        $client->followRedirect();

        $this->assertContains(
            'created_successfully',
            $client->getResponse()->getContent()
        );
    }

    /**
     * Delete Page Fr
     */
    public function testDeleteTranslationPage()
    {
        $client = static::createClient();
        $routeFr = "en/admin/content/".$this->selectContentByTitle($this->nameTitle."Page")->getId(
            )."/translations/".$this->selectContentByTitle($this->nameTitle."Page Fr Edit")->getId()."/edit/es/fr";
        $crawler = $client->request('GET', $routeFr);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $buttonCrawler = $crawler->selectButton('Delete')->form();
        $client->submit($buttonCrawler);

        $this->assertEquals(200, $client->getResponse()->isRedirect());
        $client->followRedirect();

        $this->assertContains(
            ' admin_content_home.',
            $client->getResponse()->getContent()
        );
    }

    /**
     * Delete Parent Page
     */
    public function testDeletePage()
    {
        $client = static::createClient();
        $route = "en/admin/content/".$this->selectContentByTitle($this->nameTitle."Page")->getId()."/edit/";
        $crawler = $client->request('GET', $route);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $buttonCrawler = $crawler->selectButton('Delete')->form();
        $client->submit($buttonCrawler);

        $this->assertEquals(200, $client->getResponse()->isRedirect());
        $client->followRedirect();

        $this->assertContains(
            'admin_content_home',
            $client->getResponse()->getContent()
        );
    }

    /**
     * Creation a Post
     */
    public function testNewPostAction()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', 'en/admin/content/post/new/');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $buttonCrawler = $crawler->selectButton('Add content - post')->form();

        $buttonCrawler['content_form[title]'] = $this->nameTitle."Post";
        $buttonCrawler['content_form[content]'] = $this->content;
        $idSelect = $crawler->filter('#content_form_category option')->last()->attr('value');
        $buttonCrawler['content_form[category]'] = $idSelect;

        $client->submit($buttonCrawler);

        $this->assertEquals(200, $client->getResponse()->isRedirect());
        $client->followRedirect();

        $this->assertContains(
            'created_successfully',
            $client->getResponse()->getContent()
        );
    }

    /**
     * Creation a Translation Post En
     */
    public function testTranslationPostEnAction()
    {
        $client = static::createClient();

        $routeEn = "en/admin/content/".$this->selectContentByTitle($this->nameTitle."Post")->getId(
            )."/translations/add/es/en";
        $crawler = $client->request('GET', $routeEn);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $buttonCrawler = $crawler->selectButton('Add content - post')->form();
        $buttonCrawler['content_form[title]'] = $this->nameTitle."Post En";
        $buttonCrawler['content_form[content]'] = $this->content;

        $client->submit($buttonCrawler);

        $this->assertEquals(200, $client->getResponse()->isRedirect());
        $client->followRedirect();

        $this->assertContains(
            'created_successfully',
            $client->getResponse()->getContent()
        );
    }

    /**
     * Creation a Translation Page Fr
     */
    public function testTranslationPostFrAction()
    {
        $client = static::createClient();

        $routeFr = "en/admin/content/".$this->selectContentByTitle($this->nameTitle."Post")->getId(
            )."/translations/add/es/fr";
        $crawler = $client->request('GET', $routeFr);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $buttonCrawler = $crawler->selectButton('Add content - post')->form();
        $buttonCrawler['content_form[title]'] = $this->nameTitle."Post Fr";
        $buttonCrawler['content_form[content]'] = $this->content;

        $client->submit($buttonCrawler);

        $this->assertEquals(200, $client->getResponse()->isRedirect());
        $client->followRedirect();

        $this->assertContains(
            'created_successfully',
            $client->getResponse()->getContent()
        );
    }

    /**
     * Edit a Translation Post Fr
     */
    public function testEditTranslationPostFrAction()
    {
        $client = static::createClient();

        $routeFr = "en/admin/content/".$this->selectContentByTitle($this->nameTitle."Post")->getId(
            )."/translations/".$this->selectContentByTitle($this->nameTitle."Post Fr")->getId()."/edit/es/fr";
        $crawler = $client->request('GET', $routeFr);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $buttonCrawler = $crawler->selectButton('Edit content - post')->form();
        $buttonCrawler['content_form[title]'] = $this->nameTitle."Post Fr Edit";

        $client->submit($buttonCrawler);

        $this->assertEquals(200, $client->getResponse()->isRedirect());
        $client->followRedirect();

        $this->assertContains(
            'created_successfully',
            $client->getResponse()->getContent()
        );
    }

    /**
     * Delete Post Fr
     */
    public function testDeleteTranslationPost()
    {
        $client = static::createClient();
        $routeFr = "en/admin/content/".$this->selectContentByTitle($this->nameTitle."Post")->getId(
            )."/translations/".$this->selectContentByTitle($this->nameTitle."Post Fr Edit")->getId()."/edit/es/fr";
        $crawler = $client->request('GET', $routeFr);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $buttonCrawler = $crawler->selectButton('Delete')->form();
        $client->submit($buttonCrawler);

        $this->assertEquals(200, $client->getResponse()->isRedirect());
        $client->followRedirect();

        $this->assertContains(
            ' admin_content_home.',
            $client->getResponse()->getContent()
        );
    }

    /**
     * Delete Post
     */
    public function testDeletePost()
    {
        $client = static::createClient();
        $route = "en/admin/content/".$this->selectContentByTitle($this->nameTitle."Post")->getId()."/edit/";
        $crawler = $client->request('GET', $route);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $buttonCrawler = $crawler->selectButton('Delete')->form();
        $client->submit($buttonCrawler);

        $this->assertEquals(200, $client->getResponse()->isRedirect());
        $client->followRedirect();

        $this->assertContains(
            'admin_content_home',
            $client->getResponse()->getContent()
        );
    }

    public function testDestructInitialCategory()
    {
        $client = static::createClient();
        $route = "/en/admin/category/".$this->selectCategoryByName($this->nameCategory)->getId()."/edit/";
        $crawler = $client->request('GET', $route);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $buttonCrawler = $crawler->selectButton('Delete')->form();
        $client->submit($buttonCrawler);

        $this->assertEquals(200, $client->getResponse()->isRedirect());
        $client->followRedirect();

        $this->assertContains(
            'admin_category_home.',
            $client->getResponse()->getContent()
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