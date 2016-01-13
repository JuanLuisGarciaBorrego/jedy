<?php

namespace AppBundle\Twig;

use AppBundle\Entity\Content;
use AppBundle\Util\Locales;
use Symfony\Bridge\Twig\Extension\RoutingExtension;

class AppPublicExtension extends \Twig_Extension
{
    /**
     * @var string
     */
    private $locales;

    /**
     * @var RoutingExtension
     */
    private $routingExtenxion;

    /**
     * @param Locales $locales
     * @param RoutingExtension $routingExtenxion
     */
    public function __construct(Locales $locales, RoutingExtension $routingExtenxion)
    {
        $this->locales = $locales;
        $this->routingExtenxion = $routingExtenxion;
    }

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction(
                'translation_content',
                [$this, 'translation_content'],
                ['is_safe' => ['html']]
            ),
        );
    }

    /**
     * @param Content $content
     * @param string $class
     * @return string
     */
    public function translation_content(Content $content, $class = 'translation_content')
    {
        $result = "<ul class='".$class."'>";
        foreach ($this->getTranslations($content) as $item) {
            $result .= "<li><a href='".$this->routingExtenxion->getPath(
                    'app_blog_post',
                    ['_locale' => $item['locale'], 'slug' => $item['slug']]
                )."' title='".$item['title']."'>".$item['language']."</a> </li>";
        }
        $result .= "</ul>";

        return $result;
    }

    private function getTranslations(Content $content)
    {
        $contents = [];

        if ($content->getParentMultilangue()) {
            $contents[] = [
                'locale' => $content->getParentMultilangue()->getLocale(),
                'language' => $this->locales->getLanguage($content->getParentMultilangue()->getLocale()),
                'slug' => $content->getParentMultilangue()->getSlug(),
                'title' => $content->getParentMultilangue()->getTitle(),
            ];


            foreach ($content->getParentMultilangue()->getChildrenMultilangue() as $item) {

                if ($item->getLocale() != $content->getLocale()) {
                    $contents[] = [
                        'locale' => $item->getLocale(),
                        'language' => $this->locales->getLanguage($item->getLocale()),
                        'slug' => $item->getSlug(),
                        'title' => $item->getTitle(),
                    ];
                }
            }
        }

        if ($content->getChildrenMultilangue()) {

            foreach ($content->getChildrenMultilangue() as $item) {
                $contents[] = [
                    'locale' => $item->getLocale(),
                    'language' => $this->locales->getLanguage($item->getLocale()),
                    'slug' => $item->getSlug(),
                    'title' => $item->getTitle(),
                ];
            }

        }

        return $contents;
    }

    public function getName()
    {
        return 'app_public.twig.extension';
    }
}