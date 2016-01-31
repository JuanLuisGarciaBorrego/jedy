<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Validator\Constraints as AppAssert;

/**
 * Content.
 *
 * @ORM\Table(name="content")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ContentRepository")
 */
class Content
{
    /**
     * Pagination post.
     */
    const NUM_ITEMS = 10;

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
     *
     * @Assert\NotBlank()
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="slug", type="string")
     */
    private $slug;

    /**
     * @var string
     *
     * @ORM\Column(name="summary", type="text", nullable=true)
     */
    private $summary;

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="text")
     *
     * @Assert\NotBlank()
     * @Assert\Length(min = "10")
     */
    private $content;

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
     * @ORM\Column(type="datetime")
     * @Assert\DateTime()
     */
    private $publishedAt;

    /**
     * @ORM\Column(type="boolean")
     */
    private $status;

    /**
     * @ORM\ManyToOne(targetEntity="Category", inversedBy="contents")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id")
     *
     * @AppAssert\HasTranslationParent(
     *     message="category.translation_parent"
     * )
     */
    private $category;

    /**
     * @ORM\OneToMany(targetEntity="Content", mappedBy="parentMultilangue", cascade={"persist", "remove"}, fetch="EAGER")
     */
    private $childrenMultilangue;

    /**
     * @ORM\ManyToOne(targetEntity="Content", inversedBy="childrenMultilangue", fetch="EAGER")
     * @ORM\JoinColumn(name="parent_multilangue_id", referencedColumnName="id")
     */
    private $parentMultilangue;

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    public function __construct($locale = null)
    {
        $this->childrenMultilangue = new ArrayCollection();

        $this->setLocale($locale);
    }

    /**
     * Set title.
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
     * Get title.
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
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
     * Set slug.
     *
     * @param string $slug
     *
     * @return Category
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Set summary.
     *
     * @param string $summary
     *
     * @return Category
     */
    public function setSummary($summary)
    {
        $this->summary = $summary;

        return $this;
    }

    /**
     * Get summary.
     *
     * @return string
     */
    public function getSummary()
    {
        return $this->summary;
    }

    /**
     * Set content.
     *
     * @param string $content
     *
     * @return Category
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content.
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set locale.
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
     * Get locale.
     *
     * @return string
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * Set type.
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
     * Get type.
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set category.
     *
     * @param Category|\DateTime $publishedAt
     *
     * @return Content
     */
    public function setPublishedAt(\DateTime $publishedAt)
    {
        $this->publishedAt = $publishedAt;

        return $this;
    }

    /**
     * Get publishedAt.
     *
     * @return string
     */
    public function getPublishedAt()
    {
        return $this->publishedAt;
    }

    /**
     * Set category.
     *
     * @param Category|bool $status
     *
     * @return Content
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status.
     *
     * @return bool
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set category.
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
     * Get category.
     *
     * @return \AppBundle\Entity\Category
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Add childrenMultilangue.
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
     * Remove childrenMultilangue.
     *
     * @param \AppBundle\Entity\Content $childrenMultilangue
     */
    public function removeChildrenMultilangue(Content $childrenMultilangue)
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
        return $this->childrenMultilangue->toArray();
    }

    /**
     * Set parentMultilangue.
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
     * Get parentMultilangue.
     *
     * @return \AppBundle\Entity\Content
     */
    public function getParentMultilangue()
    {
        return $this->parentMultilangue;
    }

    public function __toString()
    {
        return $this->getTitle().' ['.$this->getLocale().']';
    }
}
