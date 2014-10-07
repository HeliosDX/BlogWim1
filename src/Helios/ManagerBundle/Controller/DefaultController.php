<?php

namespace Helios\ManagerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\Form;
use JMS\SecurityExtraBundle\Annotation\Secure;

use Helios\BlogBundle\Entity\Article;
use Helios\BlogBundle\Entity\Avatar;
use Helios\BlogBundle\Form\ArticleType;
use Helios\BlogBundle\Form\ArticleEditType;
use Helios\BlogBundle\Bigbrother\BigbrotherEvents;
use Helios\BlogBundle\Bigbrother\MessagePostEvent;

use Helios\ManagerBundle\Entity\Blog;

use Helios\UserBundle\Entity\User;

use Helios\ManagerBundle\Form\BlogType;

use Stfalcon\Bundle\TinymceBundle;

class DefaultController extends Controller
{
    public function indexAction()
    {
        $blog = new Blog;
        /*if ($this->getUser()) {
            // On définit le User par défaut dans le formulaire (utilisateur courant)
            $blog->setUser($this->getUser());
        }*/

        // On crée le formulaire grâce à l'ArticleType
        $form = $this->createForm(new BlogType(), $blog);

        // On récupère la requête
        $request = $this->getRequest();

        // On vérifie qu'elle est de type POST
        if ($request->getMethod() == 'POST') {
            // On fait le lien Requête <-> Formulaire
            $form->bind($request);

            // On vérifie que les valeurs rentrées sont correctes
            if ($form->isValid()) {
                $avatar = new Avatar;
                $avatar->setUrl("default");
                $avatar->setAlt("default");
                $avatar->setPositionX(0);
                $avatar->setPositionY(0);

                $blog->setDate(new \DateTime('now'));
                $blog->setName($blog->getUser()->getUsername());
                $blog->setDescription("Default");
                $blog->setAvatar($avatar);
                // On enregistre l'objet $article dans la base de données
                $em = $this->getDoctrine()->getManager();
                $em->persist($blog);
                $em->flush();


                // On définit un message flash
                $this->get('session')->getFlashBag()->add('info', 'Vous devez activer votre compte avant de pouvoir vous connecter. Le mail d\'activation devrait arriver à l\'adresse indiquée dans quelques minutes.');

                // On redirige vers la page de visualisation de l'article nouvellement créé
                return $this->redirect($this->generateUrl('heliosblog_accueil', array('blog' => $blog->getUser()->getUsername())));
            }
        }

        // À ce stade :
        // - soit la requête est de type GET, donc le visiteur vient d'arriver sur la page et veut voir le formulaire
        // - soit la requête est de type POST, mais le formulaire n'est pas valide, donc on l'affiche de nouveau

        return $this->render('HeliosManagerBundle:Default:index.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    public function gestionAction()
    {
        return $this->render('HeliosManagerBundle:Default:gestion.html.twig');
    }

    /*
     * Affiche les blogs crées dans le menu "gestion blogs"
     */
    public function menuListeBlogsAction($user)
    {
        $em = $this->getDoctrine()->getManager();

        $listeblogs = $em->getRepository('HeliosManagerBundle:Blog')->findByUser($user);

        return $this->render('HeliosManagerBundle:Default:menu_listeblogs.html.twig', array(
            'liste_blogs' => $listeblogs // C'est ici tout l'intérêt : le contrôleur passe les variables nécessaires au template !
        ));
    }

    /*
     * Affiche les blogs crées dans le menu "gestion blogs"
     */
    public function listeBlogsAction($user)
    {
        $em = $this->getDoctrine()->getManager();

        $listeblogs = $em->getRepository('HeliosManagerBundle:Blog')->findByUser($user);

        return $this->render('HeliosManagerBundle:Default:listeblogs.html.twig', array(
            'liste_blogs' => $listeblogs
        ));
    }

    /*public function listeBlogsAction(User $user, Form $form = null)
    {
        // On récupère l'EntityManager
        $em = $this->getDoctrine()->getManager();

        // On récupère la liste des blogs de l'utilisateur
        $blogs = $em->getRepository('HeliosMangerBundle:Blog')
            ->getByUser($user->getId());

        if (null === $form) {
            $form = $this->getBlogForm($user);
        }

        return $this->render('HeliosManagerBundle:Default:listeblogs.html.twig', array(
            'user'      => $user,
            'form'         => $form->createView(),
            'blog' => $blogs
        ));
    }*/


    public function ajouterBlogAction()
    {
        $blog = new Blog;
        /*if ($this->getUser()) {
            // On définit le User par défaut dans le formulaire (utilisateur courant)
            $blog->setUser($this->getUser());
        }*/

        // On crée le formulaire grâce à l'ArticleType
        $form = $this->createForm(new BlogType(), $blog);

        // On récupère la requête
        $request = $this->getRequest();

        // On vérifie qu'elle est de type POST
        if ($request->getMethod() == 'POST') {
            // On fait le lien Requête <-> Formulaire
            $form->bind($request);

            // On vérifie que les valeurs rentrées sont correctes
            if ($form->isValid()) {
                // On enregistre l'objet $article dans la base de données
                $em = $this->getDoctrine()->getManager();
                $em->persist($blog);
                $em->flush();


                // On définit un message flash
                $this->get('session')->getFlashBag()->add('info', 'Article bien ajouté');

                // On redirige vers la page de visualisation de l'article nouvellement créé
                return $this->redirect($this->generateUrl('heliosmanager_gestion'));
            }
        }

        // À ce stade :
        // - soit la requête est de type GET, donc le visiteur vient d'arriver sur la page et veut voir le formulaire
        // - soit la requête est de type POST, mais le formulaire n'est pas valide, donc on l'affiche de nouveau

        return $this->render('HeliosManagerBundle:Default:ajouterblog.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    public function modifierBlogAction(Blog $blog)
    {
        $listeAc = array();
        foreach ($article->getArticleCompetences() as $ac) {
            $listeAc[] = $ac;
        }

        // On utilise le ArticleEditType
        $form = $this->createForm(new ArticleEditType(), $article);

        $request = $this->getRequest();

        if ($request->getMethod() == 'POST') {
            $form->bind($request);

            if ($form->isValid()) {

                $article->getArticleCompetences()->clear();

                // On enregistre l'article
                $em = $this->getDoctrine()->getManager();
                $em->persist($article);
                $em->flush();

                foreach ($form->get('articleCompetences')->getData() as $ac) {
                    $ac->setArticle($article);
                    $em->persist($ac);
                }
                // Et on supprime les articleCompetences qui existaient au début mais plus maintenant
                foreach ($listeAc as $originalAc) {
                    foreach ($form->get('articleCompetences')->getData() as $ac) {
                        // Si $originalAc existe dans le formulaire, on sort de la boucle car pas besoin de la supprimer
                        if ($originalAc == $ac) {
                            continue 2;
                        }
                    }
                    // $originalAc n'existe plus dans le formulaire, on la supprime
                    $em->remove($originalAc);
                }
                $em->flush();

                // On définit un message flash
                $this->get('session')->getFlashBag()->add('info', 'Article bien modifié');

                return $this->redirect($this->generateUrl('heliosblog_voir', array('slug' => $article->getSlug())));
            }
        }

        return $this->render('HeliosBlogBundle:Blog:modifier.html.twig', array(
            'form'    => $form->createView(),
            'article' => $article
        ));
    }

    public function supprimerBlogAction(Blog $blog)
    {
        // On crée un formulaire vide, qui ne contiendra que le champ CSRF
        // Cela permet de protéger la suppression d'article contre cette faille
        $form = $this->createFormBuilder()->getForm();

        $request = $this->getRequest();
        if ($request->getMethod() == 'POST') {
            $form->bind($request);

            if ($form->isValid()) { // Ici, isValid ne vérifie donc que le CSRF
                // On supprime le blog
                $em = $this->getDoctrine()->getManager();
                $em->remove($blog);
                $em->flush();

                // On définit un message flash
                $this->get('session')->getFlashBag()->add('info', 'Article bien supprimé');

                // Puis on redirige vers l'accueil
                return $this->redirect($this->generateUrl('heliosmanager_gestion'));
            }
        }

        // Si la requête est en GET, on affiche une page de confirmation avant de supprimer
        return $this->render('HeliosManagerBundle:Default:supprimerBlog.html.twig', array(
            'blog' => $blog,
            'form'    => $form->createView()
        ));
    }

}
