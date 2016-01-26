<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ContentsNav.
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
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=80)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="slug", type="string", length=120)
     */
    private $slug;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=20)
     */
    private $type;

    /**
     * @var int
     *
     * @ORM\Column(name="sort", type="integer")
     */
    private $sort;

    /**
     * @var int
     *
     * @ORM\Column(name="parent_id", type="integer", nullable=true)
     */
    private $parent;

    /**
     * @ORM\ManyToOne(targetEntity="Nav", inversedBy="contentsNav")
     * @ORM\JoinColumn(name="nav_id", referencedColumnName="id")
     */
    private $nav;

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set idElement.
     *
     * @param int $idElement
     *
     * @return ContentsNav
     */
    public function setIdElement($idElement)
    {
        $this->idElement = $idElement;

        return $this;
    }

    /**
     * Get idElement.
     *
     * @return int
     */
    public function getIdElement()
    {
        return $this->idElement;
    }

    /**
     * Set name.
     *
     * @param string $name
     *
     * @return ContentsNav
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set slug.
     *
     * @param string $slug
     *
     * @return ContentsNav
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug.
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set type.
     *
     * @param string $type
     *
     * @return ContentsNav
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type.
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set sort.
     *
     * @param int $sort
     *
     * @return ContentsNav
     */
    public function setSort($sort)
    {
        $this->sort = $sort;

        return $this;
    }

    /**
     * Get sort.
     *
     * @return int
     */
    public function getSort()
    {
        return $this->sort;
    }

    /**
     * Set parent.
     *
     * @param int $parent
     *
     * @return ContentsNav
     */
    public function setParent($parent)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get parent.
     *
     * @return int
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Set nav.
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
     * Get nav.
     *
     * @return Nav
     */
    public function getNav()
    {
        return $this->nav;
    }

    public function __toString()
    {
        return $this->getIdElement().' ['.$this->getId().']';
    }
}
