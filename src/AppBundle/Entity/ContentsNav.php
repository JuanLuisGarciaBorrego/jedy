<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ContentsNav
 *
 * @ORM\Table(name="contents_nav")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ContentsNavRepository")
 */
class ContentsNav
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="idElement", type="integer")
     */
    private $idElement;

    /**
     * @var int
     *
     * @ORM\Column(name="sort", type="integer")
     */
    private $sort;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set idElement
     *
     * @param integer $idElement
     *
     * @return ContentsNav
     */
    public function setIdElement($idElement)
    {
        $this->idElement = $idElement;

        return $this;
    }

    /**
     * Get idElement
     *
     * @return int
     */
    public function getIdElement()
    {
        return $this->idElement;
    }

    /**
     * Set sort
     *
     * @param integer $sort
     *
     * @return ContentsNav
     */
    public function setSort($sort)
    {
        $this->sort = $sort;

        return $this;
    }

    /**
     * Get sort
     *
     * @return int
     */
    public function getSort()
    {
        return $this->sort;
    }
}

