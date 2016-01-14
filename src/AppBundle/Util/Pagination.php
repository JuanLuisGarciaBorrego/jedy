<?php

namespace AppBundle\Util;

use AppBundle\Entity\Content;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Pagination
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function pagination($type, $page, $locale)
    {
        $total = $this->em->getRepository('AppBundle:Content')->getTotalRegisters(
            $locale,
            $type
        );

        $totalPages = ceil($total / Content::NUM_ITEMS);

        if ($totalPages != 0 && ($page > $totalPages || $page <= 0)) {
            throw new NotFoundHttpException("There are only ".$totalPages." pages to show");
        }

        $offset = Content::NUM_ITEMS * ($page - 1);
        $contents = $this->em->getRepository('AppBundle:Content')->getResultsPaginated(
            $offset,
            Content::NUM_ITEMS,
            $locale,
            $type
        );

        return [
            'contents' => $contents,
            'total' => $total,
            'totalPages' => $totalPages,
            'type' => $type,
            'locale' => $locale
        ];
    }
}