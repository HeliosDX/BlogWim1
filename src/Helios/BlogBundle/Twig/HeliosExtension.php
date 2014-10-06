<?php
// src/Helios/BlogBundle/Twig/HeliosExtension.php
namespace Helios\BlogBundle\Twig;

class HeliosExtension extends \Twig_Extension
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var User
     */
    protected $user;

    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * Constructor
     *
     * @param ContainerInterface $container
     */
    public function __construct($container, $em)
    {
        $this->container = $container;
        $this->em = $em;

        if ($this->container->isScopeActive('request')) {
            $this->request = $this->container->get('request');
        }
    }

    public function getName()
    {
        return 'blog_extension';
    }

    public function getFunctions()
    {
        return array(
            'get_blog' => new \Twig_Function_Method($this, 'getBlog'),
            'get_bloguser' => new \Twig_Function_Method($this, 'getBlogUser'),
            'get_session' => new \Twig_Function_Method($this, 'getSession')
        );
    }

    public function getBlog()
    {
        $user = $this->em->getRepository('HeliosUserBundle:User')
            ->findOneByUsername($this->request->get('blog'));

        return $this->em->getRepository('HeliosManagerBundle:Blog')
            ->findOneByUser($user);
    }

    public function getBlogUser()
    {
        return $this->em->getRepository('HeliosManagerBundle:Blog')
            ->findOneByUser($this->container->get('security.context')->getToken()->getUser());
    }

    public function getSession()
    {
        return $this->request->getSession()
            ->set('nickname', $this->container->get('security.context')->getToken()->getUser());
    }

}