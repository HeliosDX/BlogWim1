<?php

namespace Helios\BlogBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Notification
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Helios\BlogBundle\Entity\NotificationRepository")
 */
class Notification
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
     * @ORM\Column(name="notif", type="text")
     */
    private $notif;

    /**
     * @ORM\ManyToOne(targetEntity="Helios\ManagerBundle\Entity\Blog", inversedBy="notifs")
     */
    private $blog;

    /**
     * @ORM\OneToOne(targetEntity="Helios\BlogBundle\Entity\Article", cascade={"persist"})
     */
    private $article;

    /**
     * @ORM\OneToMany(targetEntity="Helios\BlogBundle\Entity\Commentaire", mappedBy="notification")
     */
    private $commentaires; // Ici commentaires prend un "s", car une notif a plusieurs commentaires !


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
     * Set notif
     *
     * @param string $notif
     * @return Notification
     */
    public function setNotif($notif)
    {
        $this->notif = $notif;
    
        return $this;
    }

    /**
     * Get notif
     *
     * @return string 
     */
    public function getNotif()
    {
        return $this->notif;
    }

    /**
     * Set dest
     *
     * @param string $dest
     * @return Notification
     */
    public function setDest($dest)
    {
        $this->dest = $dest;
    
        return $this;
    }

    /**
     * Get dest
     *
     * @return string 
     */
    public function getDest()
    {
        return $this->dest;
    }

    /**
     * Set sender
     *
     * @param string $sender
     * @return Notification
     */
    public function setSender($sender)
    {
        $this->sender = $sender;
    
        return $this;
    }

    /**
     * Get sender
     *
     * @return string 
     */
    public function getSender()
    {
        return $this->sender;
    }

    /**
     * Set blog
     *
     * @param \Helios\ManagerBundle\Entity\Blog $blog
     *
     * @return Notification
     */
    public function setBlog(\Helios\ManagerBundle\Entity\Blog $blog = null)
    {
        $this->blog = $blog;

        return $this;
    }

    /**
     * Get blog
     *
     * @return \Helios\ManagerBundle\Entity\Blog 
     */
    public function getBlog()
    {
        return $this->blog;
    }

    /**
     * Set idarticle.

     *
     * @param integer $idarticle
     *
     * @return Notification
     */
    public function setIdarticle($idarticle)
    {
        $this->idarticle = $idarticle;

        return $this;
    }

    /**
     * Get idarticle.

     *
     * @return integer
     */
    public function getIdarticle()
    {
        return $this->idarticle;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->commentaires = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add commentaire
     *
     * @param \Helios\BlogBundle\Entity\Commentaire $commentaire
     *
     * @return Notification
     */
    public function addCommentaire(\Helios\BlogBundle\Entity\Commentaire $commentaire)
    {
        $this->commentaires[] = $commentaire;
    
        return $this;
    }

    /**
     * Remove commentaire
     *
     * @param \Helios\BlogBundle\Entity\Commentaire $commentaire
     */
    public function removeCommentaire(\Helios\BlogBundle\Entity\Commentaire $commentaire)
    {
        $this->commentaires->removeElement($commentaire);
    }

    /**
     * Get commentaires
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCommentaires()
    {
        return $this->commentaires;
    }

    /**
     * Set article
     *
     * @param \Helios\BlogBundle\Entity\Article $article
     *
     * @return Notification
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
