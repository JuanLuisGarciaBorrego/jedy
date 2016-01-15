<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
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
     * @ORM\OneToMany(targetEntity="ContentsNav", mappedBy="parent", cascade={"persist", "remove"})
     */
    private $children;

    /**
     * @ORM\ManyToOne(targetEntity="ContentsNav", inversedBy="children")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id")
     */
    private $parent;

    /**
     * @ORM\ManyToOne(targetEntity="Nav", inversedBy="contentsNav")
     * @ORM\JoinColumn(name="nav_id", referencedColumnName="id")
     */
    private $nav;

    public function __construct()
    {
        $this->children = new ArrayCollection();
    }

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

    /**
     * Add child
     *
     * @param ContentsNav $child
     *
     * @return ContentsNav
     */
    public function addChild(ContentsNav $child)
    {
        $this->children[] = $child;

        return $this;
    }

    /**
     * Remove child
     *
     * @param ContentsNav $child
     */
    public function removeChild(ContentsNav $child)
    {
        $this->children->removeElement($child);
    }

    /**
     * Get children
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * Set parent
     *
     * @param ContentsNav $parent
     *
     * @return ContentsNav
     */
    public function setParent(ContentsNav $parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get parent
     *
     * @return ContentsNav
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Set nav
     *
     * @param Nav $nav
     *
     * @return ContentsNav
     */
    public function setNav(Nav $nav = null)
    {
        $this->nav = $nav;

        return $this;
    }

    /**
     * Get nav
     *
     * @return Nav
     */
    public function getNav()
    {
        return $this->nav;
    }

    function __toString()
    {
        return $this->getIdElement()." [".$this->getId()."]";
    }
}
