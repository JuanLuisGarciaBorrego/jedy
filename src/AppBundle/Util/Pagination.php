<?php

namespace AppBundle\Util;

use AppBundle\Entity\Content;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class Pagination
 * @package AppBundle\Util
 */
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

    /**
     * @param $type
     * @param $page
     * @param $locale
     * @param null $status
     * @param null $category
     *
     * @return array
     */
    public function create($type, $page, $locale, $status = null, $category = null)
    {
        $total = $this->em->getRepository('AppBundle:Content')->getTotalRegisters(
            $locale,
            $type,
            $status,
            $category
        );

        $totalPages = ceil($total / Content::NUM_ITEMS);

        if ($totalPages != 0 && ($page > $totalPages || $page <= 0)) {
            throw new NotFoundHttpException('There are only '.$totalPages.' pages to show');
        }

        $offset = Content::NUM_ITEMS * ($page - 1);
        $contents = $this->em->getRepository('AppBundle:Content')->getResultsPaginated(
            $offset,
            Content::NUM_ITEMS,
            $locale,
            $type,
            $status,
            $category
        );

        return [
            'contents' => $contents,
            'total' => $total,
            'totalPages' => $totalPages,
            'type' => $type,
            'locale' => $locale,
        ];
    }
}
