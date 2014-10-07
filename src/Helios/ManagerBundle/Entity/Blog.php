<?php

namespace Helios\ManagerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Blog
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Helios\ManagerBundle\Entity\BlogRepository")
 */
class Blog
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @ORM\OneToOne(targetEntity="Helios\BlogBundle\Entity\Avatar", cascade={"persist", "remove"})
     */
    private $avatar;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255)
     */
    private $description;

    /**
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;

    /**
     * @ORM\OneToMany(targetEntity="Helios\BlogBundle\Entity\Article", mappedBy="blog")
     */
    private $articles;

    /**
     * @ORM\OneToMany(targetEntity="Helios\BlogBundle\Entity\Notification", mappedBy="blog")
     */
    private $notifs;

    /**
     * @ORM\OneToOne(targetEntity="Helios\UserBundle\Entity\User", cascade={"persist"})
     */
    private $user;



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
     * Set name
     *
     * @param string $name
     * @return Blog
     */
    public function setName($name)
    {
        $this->name = $name;
    
        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Blog
     */
    public function setDescription($description)
    {
        $this->description = $description;
    
        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set meta
     *
     * @param string $meta
     * @return Blog
     */
    public function setMeta($meta)
    {
        $this->meta = $meta;
    
        return $this;
    }

    /**
     * Get meta
     *
     * @return string 
     */
    public function getMeta()
    {
        return $this->meta;
    }

    /**
     * Set user
     *
     * @param \Helios\UserBundle\Entity\User $user
     * @return Blog
     */
    public function setUser(\Helios\UserBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \Helios\UserBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     * @return Blog
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
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->articles = new \Doctrine\Common\Collections\ArrayCollection();
        $this->amis = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Add articles
     *
     * @param \Helios\BlogBundle\Entity\Article $articles
     * @return Blog
     */
    public function addArticle(\Helios\BlogBundle\Entity\Article $articles)
    {
        $this->articles[] = $articles;
    
        return $this;
    }

    /**
     * Remove articles
     *
     * @param \Helios\BlogBundle\Entity\Article $articles
     */
    public function removeArticle(\Helios\BlogBundle\Entity\Article $articles)
    {
        $this->articles->removeElement($articles);
    }

    /**
     * Get articles
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getArticles()
    {
        return $this->articles;
    }

    /**
     * Set avatar
     *
     * @param \Helios\BlogBundle\Entity\Avatar $avatar
     * @return Blog
     */
    public function setAvatar(\Helios\BlogBundle\Entity\Avatar $avatar = null)
    {
        $this->avatar = $avatar;
    
        return $this;
    }

    /**
     * Get avatar
     *
     * @return \Helios\BlogBundle\Entity\Avatar 
     */
    public function getAvatar()
    {
        return $this->avatar;
    }

    /**
     * Add amis
     *
     * @param \Helios\UserBundle\Entity\User $ami
     * @return Blog
     */
    public function addAmi(\Helios\UserBundle\Entity\User $ami)
    {
        $ami->addAmiblog($this); // synchronously updating inverse side
        $this->amis[] = $ami;
    }

    /**
     * Remove amis
     *
     * @param \Helios\UserBundle\Entity\User $ami
     */
    public function removeAmi(\Helios\UserBundle\Entity\User $ami)
    {
        $this->amis->removeElement($ami);
    }

    /**
     * Add notifs
     *
     * @param \Helios\BlogBundle\Entity\Notification $notifs
     *
     * @return Blog
     */
    public function addNotif(\Helios\BlogBundle\Entity\Notification $notifs)
    {
        $this->notifs[] = $notifs;

        return $this;
    }

    /**
     * Remove notifs
     *
     * @param \Helios\BlogBundle\Entity\Notification $notifs
     */
    public function removeNotif(\Helios\BlogBundle\Entity\Notification $notifs)
    {
        $this->notifs->removeElement($notifs);
    }

    /**
     * Get notifs
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getNotifs()
    {
        return $this->notifs;
    }
}
