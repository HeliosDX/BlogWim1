<?php
// src/Helios/BlogBundle/Entity/Article.php

namespace Helios\BlogBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\ExecutionContextInterface;

/**
 * Helios\BlogBundle\Entity\Article
 *
 * @ORM\Table(name="article")
 * @ORM\Entity(repositoryClass="Helios\BlogBundle\Entity\ArticleRepository")
 * @ORM\HasLifecycleCallbacks()
 * @Assert\Callback(methods={"contenuValide"})
 */
class Article
{
  /**
   * @ORM\Column(name="id", type="integer")
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="AUTO")
   */
  private $id;

  /**
   * @ORM\Column(name="date", type="datetime")
   */
  private $date;

  /**
   * @ORM\Column(name="titre", type="string", length=255)
   */
  private $titre;

  /**
   * @ORM\Column(name="auteur", type="string", length=255, nullable=true)
   * Je mets cette colonne à nullable=true car maintenant on a aussi l'attribut $user
   */
  private $auteur;

  /**
   * @ORM\Column(name="publication", type="boolean")
   */
  private $publication;

  /**
   * @ORM\Column(name="contenu", type="text")
   */
  private $contenu;

    /**
     * @ORM\OneToMany(targetEntity="Helios\BlogBundle\Entity\ThumbUp", mappedBy="article")
     */
    private $likes;

  /**
   * @ORM\Column(type="date", nullable=true)
   */
  private $dateEdition;

  /**
   * @Gedmo\Slug(fields={"titre"})
   * @ORM\Column(length=128, unique=true)
   */
  private $slug;

  /**
   * @ORM\OneToOne(targetEntity="Helios\BlogBundle\Entity\Image", cascade={"persist", "remove"})
   */
  private $image;

  /**
   * @ORM\ManyToMany(targetEntity="Helios\BlogBundle\Entity\Categorie", cascade={"persist"})
   * @ORM\JoinTable(name="article_categorie")
   */
  private $categories;

    /**
     * @ORM\ManyToMany(targetEntity="Helios\BlogBundle\Entity\Tag", cascade={"persist"}, inversedBy="articles")
     * @ORM\JoinTable(name="article_tag")
     */
    private $tags;

  /**
   * @ORM\OneToMany(targetEntity="Helios\BlogBundle\Entity\Commentaire", mappedBy="article")
   */
  private $commentaires; // Ici commentaires prend un "s", car un article a plusieurs commentaires !

  /**
   * @ORM\OneToMany(targetEntity="Helios\BlogBundle\Entity\ArticleCompetence", mappedBy="article")
   */
  private $articleCompetences;

    /**
     * @ORM\OneToOne(targetEntity="Helios\BlogBundle\Entity\Notification", cascade={"persist"})
     */
    private $notification;

  /**
   * @ORM\ManyToOne(targetEntity="Helios\ManagerBundle\Entity\Blog", inversedBy="articles")
   */
  private $blog;

  /**
   * @ORM\ManyToOne(targetEntity="Helios\UserBundle\Entity\User")
   */
  private $user;

  public function __construct()
  {
    $this->publication  = true;
    $this->date         = new \Datetime;
    $this->categories   = new \Doctrine\Common\Collections\ArrayCollection();
    $this->commentaires = new \Doctrine\Common\Collections\ArrayCollection();
    $this->tags = new \Doctrine\Common\Collections\ArrayCollection();
    $this->articleCompetences = new \Doctrine\Common\Collections\ArrayCollection();
  }

  /**
   * @ORM\PreUpdate
   * Callback pour mettre à jour la date d'édition à chaque modification de l'entité
   */
  public function updateDate()
  {
    $this->setDateEdition(new \Datetime());
  }

  public function getId()
  {
    return $this->id;
  }

  public function setDate(\Datetime $date)
  {
    $this->date = $date;
  }

  public function getDate()
  {
    return $this->date;
  }

  public function setTitre($titre)
  {
    $this->titre = $titre;
  }

  public function getTitre()
  {
    return $this->titre;
  }

  public function setContenu($contenu)
  {
    $this->contenu = $contenu;
  }

  public function getContenu()
  {
    return $this->contenu;
  }

  public function setPublication($publication)
  {
    $this->publication = $publication;
  }

  public function getPublication()
  {
    return $this->publication;
  }

  public function setAuteur($auteur)
  {
    $this->auteur = $auteur;
  }

  public function getAuteur()
  {
    return $this->auteur;
  }

  public function setImage(\Helios\BlogBundle\Entity\Image $image = null)
  {
    $this->image = $image;
  }

  public function getImage()
  {
    return $this->image;
  }

  public function addCategorie(\Helios\BlogBundle\Entity\Categorie $categorie)
  {
    $this->categories[] = $categorie;
  }

  public function removeCategorie(\Helios\BlogBundle\Entity\Categorie $categorie)
  {
    $this->categories->removeElement($categorie);
  }

  public function getCategories()
  {
    return $this->categories;
  }

  public function addCommentaire(\Helios\BlogBundle\Entity\Commentaire $commentaire)
  {
    $this->commentaires[] = $commentaire;
  }

  public function removeCommentaire(\Helios\BlogBundle\Entity\Commentaire $commentaire)
  {
    $this->commentaires->removeElement($commentaire);
  }

  public function getCommentaires()
  {
    return $this->commentaires;
  }

  public function setDateEdition(\Datetime $dateEdition)
  {
    $this->dateEdition = $dateEdition;
  }

  public function getDateEdition()
  {
    return $this->dateEdition;
  }

  public function setSlug($slug)
  {
    $this->slug = $slug;
  }

  public function getSlug()
  {
    return $this->slug;
  }

  public function addArticleCompetence(\Helios\BlogBundle\Entity\ArticleCompetence $articleCompetence)
  {
    $this->articleCompetences[] = $articleCompetence;
  }

  public function removeArticleCompetence(\Helios\BlogBundle\Entity\ArticleCompetence $articleCompetence)
  {
    $this->articleCompetences->removeElement($articleCompetence);
  }

  public function getArticleCompetences()
  {
    return $this->articleCompetences;
  }

  public function setUser(\Helios\UserBundle\Entity\User $user = null)
  {
    $this->user = $user;
  }

  public function getUser()
  {
    return $this->user;
  }

  public function contenuValide(ExecutionContextInterface $context)
  {
    $mots_interdits = array('échec', 'abandon');

    // On vérifie que le contenu ne contient pas l'un des mots
    if (preg_match('#'.implode('|', $mots_interdits).'#', $this->getContenu())) {
      // La règle est violée, on définit l'erreur et son message
      // 1er argument : on dit quel attribut l'erreur concerne, ici « contenu »
      // 2e argument : le message d'erreur
      $context->addViolationAt('contenu', 'Contenu invalide car il contient un mot interdit.', array(), null);
    }
  }

    /**
     * Set blog
     *
     * @param \Helios\ManagerBundle\Entity\Blog $blog
     * @return Article
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
     * Set like
     *
     * @param integer $like
     * @return Article
     */
    public function setLike($like)
    {
        $this->like = $like;
    
        return $this;
    }

    /**
     * Get like
     *
     * @return integer 
     */
    public function getLike()
    {
        return $this->like;
    }

    /**
     * Add likes
     *
     * @param \Helios\BlogBundle\Entity\ThumbUp $likes
     * @return Article
     */
    public function addLike(\Helios\BlogBundle\Entity\ThumbUp $likes)
    {
        $this->likes[] = $likes;
    
        return $this;
    }

    /**
     * Remove likes
     *
     * @param \Helios\BlogBundle\Entity\ThumbUp $likes
     */
    public function removeLike(\Helios\BlogBundle\Entity\ThumbUp $likes)
    {
        $this->likes->removeElement($likes);
    }

    /**
     * Get likes
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getLikes()
    {
        return $this->likes;
    }

    /**
     * Add categories
     *
     * @param \Helios\BlogBundle\Entity\Categorie $categories
     *
     * @return Article
     */
    public function addCategory(\Helios\BlogBundle\Entity\Categorie $categories)
    {
        $this->categories[] = $categories;

        return $this;
    }

    /**
     * Remove categories
     *
     * @param \Helios\BlogBundle\Entity\Categorie $categories
     */
    public function removeCategory(\Helios\BlogBundle\Entity\Categorie $categories)
    {
        $this->categories->removeElement($categories);
    }

    /**
     * Add tag
     *
     * @param \Helios\BlogBundle\Entity\Tag $tag
     *
     * @return Article
     */
    public function addTag(\Helios\BlogBundle\Entity\Tag $tag)
    {
        $tag->addArticle($this); // synchronously updating inverse side
        $this->tags[] = $tag;

        return $this;
    }
    /**
     * Remove tag
     *
     * @param \Helios\BlogBundle\Entity\Tag $tag
     */
    public function removeTag(\Helios\BlogBundle\Entity\Tag $tag)
    {
        $this->tags->removeElement($tag);
    }

    /**
     * Get tags
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * Set notification
     *
     * @param \Helios\BlogBundle\Entity\Notification $notification
     *
     * @return Article
     */
    public function setNotification(\Helios\BlogBundle\Entity\Notification $notification = null)
    {
        $this->notification = $notification;
    
        return $this;
    }

    /**
     * Get notification
     *
     * @return \Helios\BlogBundle\Entity\Notification
     */
    public function getNotification()
    {
        return $this->notification;
    }
}
