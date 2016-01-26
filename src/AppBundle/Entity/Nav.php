<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Nav.
 *
 * @ORM\Table(name="nav")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\NavRepository")
 */
class Nav
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
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=80)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="locale", type="string", length=5)
     */
    private $locale;

    /**
     * @ORM\OneToMany(targetEntity="Nav", mappedBy="parentMultilangue", cascade={"persist", "remove"})
     */
    private $childrenMultilangue;

    /**
     * @ORM\ManyToOne(targetEntity="Nav", inversedBy="childrenMultilangue")
     * @ORM\JoinColumn(name="parent_multilangue_id", referencedColumnName="id")
     */
    private $parentMultilangue;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\ContentsNav", mappedBy="nav",cascade={"persist", "remove"}, fetch="EAGER")
     */
    private $contentsNav;

    public function __construct($locale = null)
    {
        $this->childrenMultilangue = new ArrayCollection();
        $this->contentsNav = new ArrayCollection();

        $this->setLocale($locale);
    }

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
     * Set name.
     *
     * @param string $name
     *
     * @return Nav
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
     * Set locale.
     *
     * @param string $locale
     *
     * @return Nav
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;

        return $this;
    }

    /**
     * Get locale.
     *
     * @return string
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * Add childrenMultilangue.
     *
     * @param \AppBundle\Entity\Nav $childrenMultilangue
     *
     * @return Nav
     */
    public function addChildrenMultilangue(Nav $childrenMultilangue)
    {
        $this->childrenMultilangue[] = $childrenMultilangue;

        return $this;
    }

    /**
     * Remove childrenMultilangue.
     *
     * @param \AppBundle\Entity\Nav $childrenMultilangue
     */
    public function removeChildrenMultilangue(Nav $childrenMultilangue)
    {
        $this->childrenMultilangue->removeElement($childrenMultilangue);
    }

    /**
     * Get childrenMultilangue.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getChildrenMultilangue()
    {
        return $this->childrenMultilangue;
    }

    /**
     * Set parentMultilangue.
     *
     * @param \AppBundle\Entity\Nav $parentMultilangue
     *
     * @return Nav
     */
    public function setParentMultilangue(Nav $parentMultilangue = null)
    {
        $this->parentMultilangue = $parentMultilangue;

        return $this;
    }

    /**
     * Get parentMultilangue.
     *
     * @return \AppBundle\Entity\Nav
     */
    public function getParentMultilangue()
    {
        return $this->parentMultilangue;
    }

    /**
     * Add contentsNav.
     *
     * @param \AppBundle\Entity\ContentsNav $contentsNav
     *
     * @return Nav
     */
    public function addContentsNav(ContentsNav $contentsNav)
    {
        $this->contentsNav[] = $contentsNav;

        return $this;
    }

    /**
     * Remove contentsNav.
     *
     * @param \AppBundle\Entity\ContentsNav $contentsNav
     */
    public function removeContentsNav(ContentsNav $contentsNav)
    {
        $this->contentsNav->removeElement($contentsNav);
    }

    /**
     * Get contentsNav.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getContentsNav()
    {
        return $this->contentsNav;
    }

    public function __toString()
    {
        return $this->getName().' ['.$this->getLocale().']';
    }
}
