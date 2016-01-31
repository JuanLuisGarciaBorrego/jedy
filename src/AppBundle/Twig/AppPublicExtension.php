<?php

namespace AppBundle\Twig;

use AppBundle\Entity\Content;
use AppBundle\Util\Locales;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Class AppPublicExtension
 *
 * All the twig functions enabled in the administration
 * - Show translations available of the content
 * - Show the navigation menu (this method has not all functionality, for now! )
 *
 * @package AppBundle\Twig
 */
class AppPublicExtension extends \Twig_Extension
{
    /**
     * @var string
     */
    private $locales;

    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    /**
     * @var Session
     */
    private $session;

    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @param Locales $locales
     * @param UrlGeneratorInterface $urlGenerator
     * @param Session $session
     * @param EntityManager $em
     */
    public function __construct(Locales $locales, UrlGeneratorInterface $urlGenerator, Session $session, EntityManager $em)
    {
        $this->locales = $locales;
        $this->urlGenerator = $urlGenerator;
        $this->session = $session;
        $this->em = $em;
    }

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('translation_content', [$this, 'translation_content'], ['is_safe' => ['html']]),
            new \Twig_SimpleFunction('nav_locale', [$this, 'nav_locale'], ['is_safe' => ['html']]),
        );
    }

    /**
     * Show translations available of the content
     *
     * @param Content $content
     * @param string  $class
     *
     * @return string
     */
    public function translation_content(Content $content, $class = 'translation_content')
    {
        $result = "<ul class='".$class."'>";
        foreach ($this->getTranslations($content) as $item) {
            if ($item['type'] == 'post') {

                $route = $this->urlGenerator->generate('app_blog_post',
                    [
                        '_locale' => $item['locale'],
                        'slugcategory' => $item['slugcategory'],
                        'slug' => $item['slug'],
                    ]
                );

            } else {
                $route = $this->urlGenerator->generate('app_page',
                    [
                        '_locale' => $item['locale'],
                        'slug' => $item['slug'],
                    ]
                );
            }

            $result .= "<li><a href='".$route."' title='".$item['title']."'>".$item['language'].'</a> </li>';
        }
        $result .= '</ul>';

        return $result;
    }

    /**
     * Show the navigation menu
     * //TODO
     * This method has not all functionality, for now!
     *
     * @param $name
     * @param $locale
     * @return string
     * @throws \Exception
     */
    public function nav_locale($name, $locale)
    {
        $contentsNav = $this->session->has($name.$locale) ? $this->session->get($name.$locale) : $this->em->getRepository('AppBundle:Nav')->findOneBy(['name' => $name, 'locale' => $locale]);

        if ($contentsNav) {
            $data = $contentsNav->getContentsNav();
            $result = "<ul class='nav navbar-nav'>";

            foreach ($data as $item) {
                if ($item->getType() == 'category') {
                    $route = $this->urlGenerator->generate('app_blog_category', [
                       'slug' => $item->getSlug()
                    ]);

                    $result .= "<li><a href='".$route."'>".$item->getName().'</a></li>';
                }
                if ($item->getType() == 'page') {
                    $route = $this->urlGenerator->generate('app_page', [
                        'slug' => $item->getSlug()
                    ]);

                    $result .= "<li><a href='".$route."'>".$item->getName().'</a></li>';
                }
            }
            $result .= '</ul>';

            if (!$this->session->has($name.$locale)) {
                $this->session->set($name.$locale, $contentsNav);
            }

            return $result;
        }
    }

    /**
     * @param Content $content
     * @return array
     */
    private function getTranslations(Content $content)
    {
        $contents = [];

        if ($content->getParentMultilangue()) {
            $contents[] = [
                'locale' => $content->getParentMultilangue()->getLocale(),
                'language' => $this->locales->getLanguage($content->getParentMultilangue()->getLocale()),
                'slug' => $content->getParentMultilangue()->getSlug(),
                'title' => $content->getParentMultilangue()->getTitle(),
                'slugcategory' => ($content->getType() == 'post') ? $content->getParentMultilangue()->getCategory()->getSlug() : null,
                'type' => $content->getType(),
            ];

            foreach ($content->getParentMultilangue()->getChildrenMultilangue() as $item) {
                if (($item->getLocale() != $content->getLocale()) && $item->getStatus()) {
                    $contents[] = [
                        'locale' => $item->getLocale(),
                        'language' => $this->locales->getLanguage($item->getLocale()),
                        'slug' => $item->getSlug(),
                        'title' => $item->getTitle(),
                        'slugcategory' => ($content->getType() == 'post') ? $item->getCategory()->getSlug() : null,
                        'type' => $item->getType(),
                    ];
                }
            }
        }

        if ($content->getChildrenMultilangue()) {
            foreach ($content->getChildrenMultilangue() as $item) {
                if ($item->getStatus()) {
                    $contents[] = [
                        'locale' => $item->getLocale(),
                        'language' => $this->locales->getLanguage($item->getLocale()),
                        'slug' => $item->getSlug(),
                        'title' => $item->getTitle(),
                        'slugcategory' => ($content->getType() == 'post') ? $item->getCategory()->getSlug() : null,
                        'type' => $item->getType(),
                    ];
                }
            }
        }

        return $contents;
    }

    public function getName()
    {
        return 'app_public.twig.extension';
    }
}
