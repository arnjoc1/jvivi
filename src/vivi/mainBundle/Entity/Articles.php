<?php

namespace vivi\mainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use vivi\mainBundle\Entity\Image;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Articles
 *
 * @ORM\Table(name="vivi_articles")
 * @ORM\Entity(repositoryClass="vivi\mainBundle\Repository\ArticlesRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Articles
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
     * @ORM\Column(name="title", type="string", length=30, unique=true)
     */
    private $title;

    /**
     * @ORM\Column(type="string")
     */
    private $slug;

    /**
     * @var text
     *
     * @ORM\Column(name="content", type="text")
     */
    private $content;

    /**
     * @var string
     *
     * @ORM\Column(name="author", type="string", length=30)
     */
    private $author;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;
    
    /**
     * @ORM\OneToOne(targetEntity="vivi\mainBundle\Entity\Image", cascade={"persist","remove"}, fetch="EAGER")
     * @ORM\JoinColumn(nullable=true)
     */
    private $image;
    
    /**
     * @ORM\ManyToMany(targetEntity="vivi\mainBundle\Entity\Category", inversedBy="articles", cascade={"persist"}, fetch="EAGER")
     * @ORM\JoinTable(name="vivi_articles_category")
     */
    private $categories;

    public function __construct()
    {
        $this->date = new \DateTime();
        $this->categories = new ArrayCollection();
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
     * Set title
     *
     * @param string $title
     *
     * @return Articles
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
     * Set content
     *
     * @param string $content
     *
     * @return Articles
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set author
     *
     * @param string $author
     *
     * @return Articles
     */
    public function setAuthor($author)
    {
        $this->author = $author;

        return $this;
    }

    /**
     * Get author
     *
     * @return string
     */
    public function getAuthor()
    {
        return $this->author;
    }


    

    /**
     * Set date
     *
     * @param \DateTime $date
     *
     * @return Articles
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    
    public function setImage($image = null)
    {
        $this->image = $image;
        
        return $this;
    }

    public function getImage()
    {
        return $this->image;
    
    }
    
    public function addCategory(Category $category)
    {
        $this->categories[] = $category;
    }
    
    public function removeCategory(Category $category)
    {
        $this->categories->removeElement($category);
    }
    
    public function getCategories()
    {
       return $this->categories;
    }

    /**
     * Set slug
     *
     * @param string $slug
     *
     * @return Articles
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }
}
