<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Validator\Constraints as AppAssert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Configuration.
 *
 * @ORM\Table(name="configuration")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ConfigurationRepository")
 */
class Configuration
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
     * @ORM\Column(name="title_site", type="string", length=80, nullable=true)
     */
    private $titleSite;

    /**
     * @var string
     *
     * @ORM\Column(name="description_site", type="string", length=250, nullable=true)
     */
    private $descriptionSite;

    /**
     * @ORM\Column(name="enable_blog", type="boolean")
     */
    private $enableBlog;

    /**
     * @ORM\ManyToOne(targetEntity="Content", inversedBy="configurations")
     * @ORM\JoinColumn(name="content_id", referencedColumnName="id", nullable=true)
     */
    private $content;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set titleSite
     *
     * @param string $titleSite
     *
     * @return Configuration
     */
    public function setTitleSite($titleSite)
    {
        $this->titleSite = $titleSite;

        return $this;
    }

    /**
     * Get titleSite
     *
     * @return string
     */
    public function getTitleSite()
    {
        return $this->titleSite;
    }

    /**
     * Set descriptionSite
     *
     * @param string $descriptionSite
     *
     * @return Configuration
     */
    public function setDescriptionSite($descriptionSite)
    {
        $this->descriptionSite = $descriptionSite;

        return $this;
    }

    /**
     * Get descriptionSite
     *
     * @return string
     */
    public function getDescriptionSite()
    {
        return $this->descriptionSite;
    }

    /**
     * Set enableBlog
     *
     * @param boolean $enableBlog
     *
     * @return Configuration
     */
    public function setEnableBlog($enableBlog)
    {
        $this->enableBlog = $enableBlog;

        return $this;
    }

    /**
     * Get enableBlog
     *
     * @return boolean
     */
    public function getEnableBlog()
    {
        return $this->enableBlog;
    }

    /**
     * Set content
     *
     * @param \AppBundle\Entity\Content $content
     *
     * @return Configuration
     */
    public function setContent(\AppBundle\Entity\Content $content = null)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return \AppBundle\Entity\Content
     */
    public function getContent()
    {
        return $this->content;
    }
}
