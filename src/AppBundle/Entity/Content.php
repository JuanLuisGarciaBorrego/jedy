<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Content
 *
 * @ORM\Table(name="content")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ContentRepository")
 */
class Content
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
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="locale", type="string", length=5)
     */
    private $locale;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=20)
     */
    private $type;

    /**
     * @ORM\ManyToOne(targetEntity="Category", inversedBy="contents")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id")
     */
    private $category;

    /**
     * @ORM\OneToMany(targetEntity="Content", mappedBy="parentMultilangue")
     */
    private $childrenMultilangue;

    /**
     * @ORM\ManyToOne(targetEntity="Content", inversedBy="childrenMultilangue")
     * @ORM\JoinColumn(name="parent_multilangue_id", referencedColumnName="id")
     */
    private $parentMultilangue;

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    public function __construct($locale = null) {
        $this->childrenMultilangue = new ArrayCollection();
        
        $this->setLocale($locale);
    }

    /**
     * Set title
     *
     * @param string $title
     *
     * @return Content
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set locale
     *
     * @param string $locale
     *
     * @return Content
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;

        return $this;
    }

    /**
     * Get locale
     *
     * @return string
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * Set type
     *
     * @param string $type
     *
     * @return Content
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set category
     *
     * @param \AppBundle\Entity\Category $category
     *
     * @return Content
     */
    public function setCategory(Category $category = null)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category
     *
     * @return \AppBundle\Entity\Category
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Add childrenMultilangue
     *
     * @param \AppBundle\Entity\Content $childrenMultilangue
     *
     * @return Content
     */
    public function addChildrenMultilangue(Content $childrenMultilangue)
    {
        $this->childrenMultilangue[] = $childrenMultilangue;

        return $this;
    }

    /**
     * Remove childrenMultilangue
     *
     * @param \AppBundle\Entity\Content $childrenMultilangue
     */
    public function removeChildrenMultilangue(Content $childrenMultilangue)
    {
        $this->childrenMultilangue->removeElement($childrenMultilangue);
    }

    /**
     * Get childrenMultilangue
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getChildrenMultilangue()
    {
        return $this->childrenMultilangue;
    }

    /**
     * Set parentMultilangue
     *
     * @param \AppBundle\Entity\Content $parentMultilangue
     *
     * @return Content
     */
    public function setParentMultilangue(Content $parentMultilangue = null)
    {
        $this->parentMultilangue = $parentMultilangue;

        return $this;
    }

    /**
     * Get parentMultilangue
     *
     * @return \AppBundle\Entity\Content
     */
    public function getParentMultilangue()
    {
        return $this->parentMultilangue;
    }
}
