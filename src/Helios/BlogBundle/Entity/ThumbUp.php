<?php

namespace Helios\BlogBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ThumbUp
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Helios\BlogBundle\Entity\ThumbUpRepository")
 */
class ThumbUp
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
     * @ORM\ManyToOne(targetEntity="Helios\UserBundle\Entity\User")
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="Helios\BlogBundle\Entity\Article", inversedBy="likes")
     */
    private $article;

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
     * Set likes
     *
     * @param integer $likes
     * @return ThumbUp
     */
    public function setLikes($likes)
    {
        $this->likes = $likes;
    
        return $this;
    }

    /**
     * Get likes
     *
     * @return integer 
     */
    public function getLikes()
    {
        return $this->likes;
    }

    /**
     * Set user
     *
     * @param \Helios\UserBundle\Entity\User $user
     * @return ThumbUp
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
     * Set article
     *
     * @param \Helios\BlogBundle\Entity\Article $article
     * @return ThumbUp
     */
    public function setArticle(\Helios\BlogBundle\Entity\Article $article = null)
    {
        $this->article = $article;
    
        return $this;
    }

    /**
     * Get article
     *
     * @return \Helios\BlogBundle\Entity\Article 
     */
    public function getArticle()
    {
        return $this->article;
    }
}
