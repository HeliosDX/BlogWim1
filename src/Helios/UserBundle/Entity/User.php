<?php

namespace Helios\UserBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="user")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToMany(targetEntity="User", cascade={"persist"}, mappedBy="myFriends")
     */
    private $friendsWithMe;

    /**
     * @ORM\ManyToMany(targetEntity="User", inversedBy="friendsWithMe")
     * @ORM\JoinTable(name="friends",
     *      joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="friend_user_id", referencedColumnName="id")}
     *      )
     **/
    private $myFriends;

    /**
     * @ORM\OneToOne(targetEntity="Helios\ManagerBundle\Entity\Blog", cascade={"persist", "remove"})
     */
    private $blog;

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
     * Set amiblog
     *
     * @param \Helios\ManagerBundle\Entity\Blog $amiblog
     * @return User
     */
    public function setAmiblog(\Helios\ManagerBundle\Entity\Blog $amiblog = null)
    {
        $this->amiblog = $amiblog;

        return $this;
    }

    /**
     * Get amiblog
     *
     * @return \Helios\ManagerBundle\Entity\Blog
     */
    public function getAmiblog()
    {
        return $this->amiblog;
    }

    /**
     * Set blog
     *
     * @param \Helios\ManagerBundle\Entity\Blog $blog
     * @return User
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
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->friendsWithMe = new \Doctrine\Common\Collections\ArrayCollection();
        $this->myFriends = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add amiblogs
     *
     * @param \Helios\ManagerBundle\Entity\Blog $amiblogs
     *
     * @return User
     */
    public function addAmiblog(\Helios\ManagerBundle\Entity\Blog $amiblogs)
    {
        $this->amiblogs[] = $amiblogs;

        return $this;
    }

    /**
     * Remove amiblogs
     *
     * @param \Helios\ManagerBundle\Entity\Blog $amiblogs
     */
    public function removeAmiblog(\Helios\ManagerBundle\Entity\Blog $amiblogs)
    {
        $this->amiblogs->removeElement($amiblogs);
    }

    /**
     * Get amiblogs
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getAmiblogs()
    {
        return $this->amiblogs;
    }

    /**
     * Add friendsWithMe
     *
     * @param \Helios\UserBundle\Entity\User $friendsWithMe
     *
     * @return User
     */
    public function addFriendsWithMe(\Helios\UserBundle\Entity\User $friendsWithMe)
    {
        $this->friendsWithMe[] = $friendsWithMe;
    
        return $this;
    }

    /**
     * Remove friendsWithMe
     *
     * @param \Helios\UserBundle\Entity\User $friendsWithMe
     */
    public function removeFriendsWithMe(\Helios\UserBundle\Entity\User $friendsWithMe)
    {
        $this->friendsWithMe->removeElement($friendsWithMe);
    }

    /**
     * Get friendsWithMe
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFriendsWithMe()
    {
        return $this->friendsWithMe;
    }

    /**
     * Add myFriend
     *
     * @param \Helios\UserBundle\Entity\User $myFriend
     *
     * @return User
     */
    public function addMyFriend(\Helios\UserBundle\Entity\User $myFriend)
    {
        $this->myFriends[] = $myFriend;
    
        return $this;
    }

    /**
     * Remove myFriend
     *
     * @param \Helios\UserBundle\Entity\User $myFriend
     */
    public function removeMyFriend(\Helios\UserBundle\Entity\User $myFriend)
    {
        $this->myFriends->removeElement($myFriend);
    }

    /**
     * Get myFriends
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getMyFriends()
    {
        return $this->myFriends;
    }
}
