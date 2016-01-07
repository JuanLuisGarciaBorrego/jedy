<?php

namespace test\AppBundle\Util;

use AppBundle\Util\Locales;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Symfony\Component\HttpFoundation\Session\Session;

class LocalesTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Locales
     */
    private $locales;

    protected function setUp()
    {
        $session = new Session(new MockArraySessionStorage());
        $session->set('locale_user', 'es');

        $this->locales = new Locales("es", "es|en|fr", $session);
    }

    public function testGetLocales()
    {
        $result = $this->locales->getLocales();
        $this->assertEquals($this->resultLocales(), $result);
    }

    public function testLocaleActive()
    {
        $result = $this->locales->getLocaleActive();
        $this->assertEquals("es", $result);
    }

    public function resultLocales()
    {
        return [
            [
                'code' => "es",
                'name' => "Español",
                'active' => true,
            ],
            [
                'code' => "en",
                'name' => "Inglés",
                'active' => false,
            ],
            [
                'code' => "fr",
                'name' => "Francés",
                'active' => false,
            ],
        ];
    }
}