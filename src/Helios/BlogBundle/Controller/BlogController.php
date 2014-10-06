<?php

// src/Helios/BlogBundle/Controller/BlogController.php

namespace Helios\BlogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\Form;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
//use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

use Helios\BlogBundle\Entity\Article;
use Helios\BlogBundle\Entity\Categorie;
use Helios\BlogBundle\Entity\Commentaire;
use Helios\BlogBundle\Entity\Tag;
use Helios\BlogBundle\Entity\ThumbUp;
use Helios\BlogBundle\Entity\ArticleCompetence;
use Helios\BlogBundle\Entity\Notification;
use Helios\BlogBundle\Entity\Avatar;
use Helios\ManagerBundle\Entity\Blog;
use Helios\UserBundle\Entity\User;

use Helios\BlogBundle\Form\ArticleType;
use Helios\BlogBundle\Form\ArticleHandler;
use Helios\BlogBundle\Form\ArticleEditType;
use Helios\BlogBundle\Form\CommentaireType;

use Helios\BlogBundle\Bigbrother\BigbrotherEvents;
use Helios\BlogBundle\Bigbrother\MessagePostEvent;

class BlogController extends Controller
{
    /*public function getRequest()
    {
        return $this->container->get('request_stack')->getCurrentRequest();
    }*/

    /*
     * Affiche la page d'accueil avec le nombre d'articles par page
     * Pour changer le nombre d'articles par page, on modifie le fichier parametres.yml
     */
  public function indexAction($blog, $page)
  {
    $em = $this->getDoctrine()->getManager();

    // On récupère le nombre d'article par page depuis un paramètre du conteneur
    // cf app/config/parameters.yml
    $nbParPage = $this->container->getParameter('heliosblog.nombre_par_page');

    // On récupère les articles de la page courante
    $articles = $em->getRepository('HeliosBlogBundle:Article')
                     ->getArticles($blog, $nbParPage, $page);

    // On passe le tout à la vue
    return $this->render('HeliosBlogBundle:Blog:index.html.twig', array(
      'articles' => $articles,
      'page'     => $page,
      'nb_page'  => ceil(count($articles) / $nbParPage) ?: 1
    ));
  }

    public function changePasswordAction()
    {
        $blog = $this->get('heliosblog.blog')->getBlogUser();

        // On récupère l'EntityManager
        $em = $this->getDoctrine()->getManager();

        $user = $this->getUser();
        //$form = $this->createForm(new \Helios\UserBundle\Form\ChangePasswordUserFormType(), $user);

        $formFactory = $this->container->get('fos_user.change_password.form.factory');
        $formChangePassword = $formFactory->createForm();
        $formChangePassword->setData($user);

        $request = $this->getRequest();

        if ($request->getMethod() == 'POST') {
            $formChangePassword->bind($request);

            if ($formChangePassword->isValid()) {

                // On enregistre l'user
                $em->persist($user);
                $em->flush();

            }
        }

        return $this->render('HeliosBlogBundle:Blog:changepassword.html.twig', array(
            'form' => $formChangePassword->createView(),
            'blog' => $blog
        ));
    }

    public function changeEmailAction()
    {
        $blog = $this->get('heliosblog.blog')->getBlogUser();

        // On récupère l'EntityManager
        $em = $this->getDoctrine()->getManager();

        $user = $this->getUser();
        //$form = $this->createForm(new \Helios\UserBundle\Form\ChangePasswordUserFormType(), $user);

        $formFactory = $this->container->get('fos_user.profile.form.factory');
        $formChangePassword = $formFactory->createForm();
        $formChangePassword->setData($user);

        $request = $this->getRequest();

        if ($request->getMethod() == 'POST') {
            $formChangePassword->bind($request);

            if ($formChangePassword->isValid()) {

                // On enregistre l'user
                $em->persist($user);
                $em->flush();

            }
        }

        return $this->render('HeliosBlogBundle:Blog:changeemail.html.twig', array(
            'form' => $formChangePassword->createView(),
            'blog' => $blog
        ));
    }

    /*
     * Affiche un article
     */
  public function voirAction($slug, Form $form = null)
  {
    // On récupère l'EntityManager
    $em = $this->getDoctrine()->getManager();

      $article = $em->getRepository('HeliosBlogBundle:Article')
          ->findOneBySlug($slug);

      if ($article==null){
          throw $this->createNotFoundException('Article non trouvé');
      }

    // On récupère la liste des commentaires
    $commentaires = $em->getRepository('HeliosBlogBundle:Commentaire')
                       ->getByArticle($article->getId());

    if (null === $form) {
      $form = $this->getCommentaireForm($article);
    }

    return $this->render('HeliosBlogBundle:Blog:voir.html.twig', array(
      'article'      => $article,
      'form'         => $form->createView(),
      'commentaires' => $commentaires
    ));
  }

  public function voirByNotifAction(Blog $blog, Article $article, Notification $notif, Form $form = null)
    {
        // On récupère l'EntityManager
        $em = $this->getDoctrine()->getManager();

        if ($article==null){
            throw $this->createNotFoundException('Article non trouvé');
        }

        // On récupère la liste des commentaires
        $commentaires = $em->getRepository('HeliosBlogBundle:Commentaire')
            ->findByNotification($notif);

        if (null === $form) {
            $form = $this->getCommentaireForm($article);
        }

        return $this->render('HeliosBlogBundle:Blog:voir_by_notif.html.twig', array(
            'article'      => $article,
            'form'         => $form->createView(),
            'blog'         => $blog,
            'commentaires' => $commentaires
        ));
    }

    /**
     * Ajoute un article
     * @Route("/{blog}")
     */
  public function ajouterAction()
  {
    $article = new Article;

    /* On récupère les élements qu'on a besoin pour notre handler qui va traiter le formulaire:
      - Le formulaire ArticleType,
      - La requête,
      - Le user courant,
      - L'entity Manager */
    $form = $this->createForm(new ArticleType(), $article);
    $request = $this->getRequest();
    $user = $this->getUser();
    $em = $this->getDoctrine()->getManager();
    $formHandler = new ArticleHandler($form, $request, $em, $user);

      if($formHandler->process())
      {
          foreach($article->getTags() as $tag)
          {
              $this->get('session')->getFlashBag()->add('info',count($tag->getArticles()));
          }
          $this->get('session')->getFlashBag()->add('info', $article->getContenu());
          $this->get('session')->getFlashBag()->add('info', count($article->getTags()));
          return $this->redirect($this->generateUrl('heliosblog_accueil', array('blog' => $request->get('blog'))));
      }

    // À ce stade :
    // - soit la requête est de type GET, donc le visiteur vient d'arriver sur la page et veut voir le formulaire
    // - soit la requête est de type POST, mais le formulaire n'est pas valide, donc on l'affiche de nouveau

    return $this->render('HeliosBlogBundle:Blog:ajouter.html.twig', array(
      'form' => $form->createView(),
    ));
  }


    /*
     * Modifie un article, à condition d'être un auteur
     */
  public function modifierAction(Article $article)
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

        return $this->redirect($this->generateUrl('heliosblog_accueil', array('blog' => $request->get('blog'))));
      }
    }

    return $this->render('HeliosBlogBundle:Blog:modifier.html.twig', array(
      'form'    => $form->createView(),
      'article' => $article
    ));
  }


    /*
     * Supprime un article, à condition d'être loggé (évidemment)
     */
  public function supprimerAction(Article $article)
  {
    // On crée un formulaire vide, qui ne contiendra que le champ CSRF
    // Cela permet de protéger la suppression d'article contre cette faille
    $form = $this->createFormBuilder()->getForm();

    $request = $this->getRequest();
    if( $request->getMethod() == 'GET') {
      $form->bind($request);

      //if ($form->isValid()) { // Ici, isValid ne vérifie donc que le CSRF
        // On supprime l'article
        $em = $this->getDoctrine()->getManager();
        $em->remove($article);
        $em->flush();

        // On définit un message flash
        $this->get('session')->getFlashBag()->add('info', 'Article bien supprimé');
          // Si la requête est en GET, on affiche une page de confirmation avant de supprimer
          return new Response ("ok");
      //}
    }
      else {
          return new Response ("Erreur lors de la suppression de l'article. Réessayez dans quelques instants.");
      }

  }


    /*
     * Ajoute un commentaire à un article
     */
  public function ajouterCommentaireAction(Article $article)
  {
    $em = $this->getDoctrine()->getManager();

    $commentaire = new Commentaire;
    $commentaire->setArticle($article);
    $commentaire->setIp($this->getRequest()->server->get('REMOTE_ADDR'));

    $blog = $this->get('heliosblog.blog')->getBlogUser();

    $form = $this->getCommentaireForm($article, $commentaire);

    $request = $this->getRequest();

    if ($request->isXmlHttpRequest()) {
        // Avec la route que l'on a, nous sommes forcément en POST ici, pas besoin de le retester
        $form->bind($request);
        if ($form->isValid()) {

            /* Si l'utilisateur a déjà écris un commentaire sur l'article et si la
               notif associée n'est pas supprimée, on ne crée pas de notif */

            $notification = $em->getRepository('HeliosBlogBundle:Notification')
                ->findOneByArticle($article);

            // Aucune notif existante pour cet article, on la crée
            if (count($notification) == 0) {

                // On ajoute une notification pour prévenir que quelqu'un a écrit un commentaire
                $notification = new Notification;
                $posteur = '<span style="color: black;">' . $this->getUser()->getUsername() . "</span><br/>";
                $contenu = $posteur . " a posté un commentaire sur votre article ";

            } // Si la notif associée à cet article existe, on la met à jour.
            else if (count($notification) == 1) {

                $commentaires = $notification->getCommentaires();
                $nb_commentaires = count($commentaires);
                if ($nb_commentaires == 1) {
                    $text_comment = "un commentaire";
                } else if ($nb_commentaires > 1) {
                    $text_comment = "des commentaires";
                }

                foreach ($commentaires as $comment) {
                    $auteurs[] = $comment->getUser()->getUsername();
                }

                $auteurs = array_unique($auteurs);

                if (count($auteurs) == 1) {
                    $posteur = '<span style="color: black;">' . $this->getUser()->getUsername() . "</span> et " . $auteurs[0] . "<br/>";
                    $contenu = $posteur . " ont posté " . $text_comment . " sur votre article ";
                } else if (count($auteurs) > 1) {
                    $posteur = '<span style="color: black;">' . $this->getUser()->getUsername() . "</span> et " . count($auteurs) . " autres personnes <br/>";
                    $contenu = $posteur . " ont posté un commentaire sur votre article ";
                }
            }

            $notification->setNotif($contenu);
            $notification->setBlog($blog);
            $notification->setArticle($article);
            $notification->addCommentaire($commentaire);

            $commentaire->setNotification($notification);
            $article->setNotification($notification);

            $em->persist($commentaire);
            $em->persist($notification);
            $em->flush();

            //$this->get('session')->getFlashBag()->add('info', 'Commentaire bien enregistré !');

            $return = array(
                "auteur" => $commentaire->getUser()->getUsername(),
                "date" => $commentaire->getDate(),
                "contenu" => $commentaire->getContenu()
            );

            $return = json_encode($return); //jscon encode the array
            return new Response($return, 200, array('Content-Type' => 'application/json'));//make sure it has the correct content type


            // On redirige vers la page de l'article, avec une ancre vers le nouveau commentaire
            //return $this->redirect($this->generateUrl('heliosblog_voir', array('slug' => $article->getSlug())).'#comment'.$commentaire->getId());
        }
    }

    // On réaffiche le formulaire sans redirection (sinon on perd les informations du formulaire)
    return $this->render('HeliosBlogBundle:Blog:add_comment.html.twig', array(
      'article' => $article,
      'blog' => $blog,
      'form'    => $form->createView(),
    ));
  }

    public function listeAmisAction(Blog $bloguser)
    {
        $em = $this->getDoctrine()->getManager();

        // On récupère les articles de la page courante
        $amis = $em->getRepository('HeliosUserBundle:User')
            ->find($bloguser->getUser());

        //$amis = $bloguser->getUser()->getMyFriends();

        // On passe le tout à la vue
        return $this->render('HeliosBlogBundle:Blog:amis.html.twig', array(
            'blog' => $bloguser,
            'amis' => $amis
        ));
    }

    /*
     * Ajoute un ami
     */
    public function ajouterAmiAction(Blog $ami, Blog $bloguser)
    {
        $bloguser->getUser()->addMyFriend($ami->getUser());

        $em = $this->getDoctrine()->getManager();
        $em->persist($bloguser);
        $em->flush();
        echo count($bloguser->getAmis());

        $this->get('session')->getFlashBag()->add('info', count($bloguser->getAmis()));
            $this->get('session')->getFlashBag()->add('info', 'Ami bien rajouté !');

            // On redirige vers la page de l'article, avec une ancre vers le nouveau commentaire
        return $this->redirect($this->generateUrl('heliosblog_accueil', array('blog' => $this->getRequest()->get('blog'))));
    }

    public function ajouterLikeAction(Article $article)
    {
        $like = new ThumbUp;
        $like->setArticle($article);
        $like->setUser($this->getUser());

        //if($this->getRequest()->isXmlHttpRequest()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($like);
            $em->flush();
        //}

        // On réaffiche le formulaire sans redirection (sinon on perd les informations du formulaire)
        //return $this->redirect($this->generateUrl('heliosblog_accueil', array('blog' => $this->getRequest()->get('blog'))));
        return new Response (count($article->getLikes()));
    }

    public function listeLikeAction(Article $article)
    {
        $like = new ThumbUp;
        $like->setArticle($article);
        $like->setUser($this->getUser());

        $em = $this->getDoctrine()->getManager();
        $em->persist($like);
        $em->flush();

        // On réaffiche le formulaire sans redirection (sinon on perd les informations du formulaire)
        return $this->redirect($this->generateUrl('heliosblog_accueil', array('blog' => $this->getRequest()->get('blog'))));
    }

  /**
   * Supprime un commentaire d'un article, à condition d'être un admin
   * @Secure(roles="ROLE_ADMIN")
   */
  public function supprimerCommentaireAction(Commentaire $commentaire)
  {
    // On crée un formulaire vide, qui ne contiendra que le champ CSRF
    // Cela permet de protéger la suppression d'article contre cette faille
    $form = $this->createFormBuilder()->getForm();

    $request = $this->getRequest();
    if ($request->getMethod() == 'POST') {
      $form->bind($request);

      if ($form->isValid()) { // Ici, isValid ne vérifie donc que le CSRF
        // On supprime l'article
        $em = $this->getDoctrine()->getManager();
        $em->remove($commentaire);
        $em->flush();

        // On définit un message flash
        $this->get('session')->getFlashBag()->add('info', 'Commentaire bien supprimé');

        // Puis on redirige vers l'accueil
        return $this->redirect($this->generateUrl('heliosblog_voir', array('slug' => $commentaire->getArticle()->getSlug())));
      }
    }

    // Si la requête est en GET, on affiche une page de confirmation avant de supprimer
    return $this->render('HeliosBlogBundle:Blog:supprimerCommentaire.html.twig', array(
      'commentaire' => $commentaire,
      'form'        => $form->createView()
    ));
  }


    /*
     * Menu de gauche qui affiche les derniers articles postés
     */
  public function menuAction($nombre)
  {
    $repository = $this->getDoctrine()->getManager()->getRepository('HeliosBlogBundle:Article');

    $liste = $repository->findBy(
      array(),                 // Pas de critère
      array('date' => 'desc'), // On tri par date décroissante
      $nombre,                 // On sélectionne $nombre articles
      0                        // A partir du premier
    );

    return $this->render('HeliosBlogBundle:Blog:menu.html.twig', array(
      'liste_articles' => $liste // C'est ici tout l'intérêt : le contrôleur passe les variables nécessaires au template !
    ));
  }

  public function traductionAction($name)
  {
    return $this->render('HeliosBlogBundle:Blog:traduction.html.twig', array(
      'name' => $name
    ));
  }

  public function feedAction()
  {
    $articles = $this->getDoctrine()
                     ->getManager()
                     ->getRepository('HeliosBlogBundle:Article')
                     ->getArticles(10, 1);

    $lastArticle = current($articles->getIterator());

    return $this->render('HeliosBlogBundle:Blog:feed.xml.twig', array(
      'articles'  => $articles,
      'buildDate' => $lastArticle->getDate()
    ));
  }

  // Méthodes protégées :

  /**
   * Retourne le formulaire d'ajout d'un commentaire
   * @param Article $article
   * @return Form
   */
  protected function getCommentaireForm(Article $article, Commentaire $commentaire = null)
  {
    if (null === $commentaire) {
      $commentaire = new Commentaire;
    }

    // Si l'utilisateur courant est identifié, on l'ajoute au commentaire
    if (null !== $this->getUser()) {
        $commentaire->setUser($this->getUser());
    }

    return $this->createForm(new CommentaireType(), $commentaire);
  }

    public function uploadAvatarAction()
    {
        $blog = $this->get('heliosblog.blog')->getBlog();
        $avatar = $blog->getAvatar();

        // On utilise le AvatarType
        $form = $this->createForm(new \Helios\BlogBundle\Form\AvatarType(), $avatar);

        $request = $this->getRequest();

        if ($request->getMethod() == 'POST') {
            $form->bind($request);
            if ($form->isValid()) {

                // On enregistre le avatar
                $em = $this->getDoctrine()->getManager();
                $avatar->setPositionY(0);
                $em->persist($avatar);
                $em->flush();

                return $this->redirect($this->generateUrl('heliosblog_accueil', array('blog' => $request->get('blog'))));
            }
        }

        return $this->render('HeliosBlogBundle:Blog:uploadavatar.html.twig', array(
            'isNew' => true,
            'form'    => $form->createView(),
            'avatarId' => $avatar->getId(),
            'blog' => $blog
        ));
    }

  public function modifierAvatarAction()
    {
        return $this->render('HeliosBlogBundle:Blog:editavatar.html.twig');
    }

    public function ajusterAvatarAction(Avatar $avatar, $positionY)
    {
        $em = $this->getDoctrine()->getManager();

        // On utilise le AvatarType
        $form = $this->createForm(new \Helios\BlogBundle\Form\AvatarType(), $avatar);

        $request = $this->getRequest();


            $form->bind($request);


                // On enregistre le avatar
                $em = $this->getDoctrine()->getManager();
                $avatar->setPositionY($positionY);
                $em->persist($avatar);
                $em->flush();

                return $this->redirect($this->generateUrl('heliosblog_accueil', array('blog' => $request->get('blog'))));
    }

  public function modifierNomAction()
    {
        $blog = $this->get('heliosblog.blog')->getBlog();

        // On utilise le AvatarType
        $form = $this->createForm(new \Helios\ManagerBundle\Form\NameEditType(), $blog);

        $request = $this->getRequest();

        if ($request->isXmlHttpRequest()) {
            $form->bind($request);
            if ($form->isValid()) {

                // On enregistre le avatar
                $em = $this->getDoctrine()->getManager();
                $em->persist($blog);
                $em->flush();

                return $this->redirect($this->generateUrl('heliosblog_accueil', array('blog' => $request->get('blog'))));
            }
        }

        return $this->render('HeliosBlogBundle:Blog:editname.html.twig', array(
            'form'    => $form->createView(),
            'blog' => $blog
        ));
    }

    public function modifierNomAjaxAction()
    {
        $blog = $this->get('heliosblog.blog')->getBlogUser();

        $form = $this->createForm(new \Helios\ManagerBundle\Form\NameEditType(), $blog);

        $request = $this->getRequest();

        if ($request->isXmlHttpRequest()) {
            $form->bind($request);
            if ($form->isValid()) {

                // On enregistre le avatar
                $em = $this->getDoctrine()->getManager();
                $em->persist($blog);
                $em->flush();

                $return = array(
                    "name"=>$blog->getName(),
                    "description"=>$blog->getDescription()
                );

                $return=json_encode($return); //jscon encode the array
                return new Response($return,200,array('Content-Type'=>'application/json'));//make sure it has the correct content type
            }
        }

        return $this->render('HeliosBlogBundle:Blog:editname.html.twig', array(
            'form'    => $form->createView(),
            'blog' => $blog
        ));
    }

  public function tagAction($tag)
  {
        $em = $this->getDoctrine()
            ->getManager();

        // On récupère le nombre d'article par page depuis un paramètre du conteneur
        // cf app/config/parameters.yml
        //$nbParPage = $this->container->getParameter('heliosblog.nombre_par_page');

      $tag = $em->getRepository('HeliosBlogBundle:Tag')
          ->findOneByTag($tag);
      $articles = $tag->getArticles();

        // Si aucun article trouvé, on affiche une belle page erreur 404
        if ($articles==null){
            throw $this->createNotFoundException('Oops ! Pas d\'aricles trouvés pour ce tag');
        }

        // On passe le tout à la vue
        return $this->render('HeliosBlogBundle:Blog:tag.html.twig', array(
            'articles' => $articles
        ));
    }

    public function validAvatarAction(Avatar $avatar, $tempFilename)
    {
        $blog = $this->get('heliosblog.blog')->getBlog();
        $blogid = $blog->getId();

        $request = $this->getRequest();

        if ($request->isXmlHttpRequest()) {

            $array = explode(".",$tempFilename);
            $tempFilename_ext = $array[1];
            $newFilename = $blogid.'.'.$tempFilename_ext;

            $root_dir = __DIR__.'/../../../../web/uploads/img/';
            $uploadedFile = __DIR__.'/../../../../web/uploads/img/'.$tempFilename;
            $updatedFile = __DIR__.'/../../../../web/uploads/img/'.$newFilename;

            // On archive les anciens avatars
            if (file_exists($updatedFile)) {
                $i = 1;
                while(file_exists($blogid.'_'.$i.'.'.$tempFilename_ext)) {
                    if (!file_exists($blogid.'_'.$i.'.'.$tempFilename_ext)) {
                        break;
                    }
                    $i++;
                }
                rename($root_dir.$newFilename, $root_dir.$blogid.'_'.$i.'.'.$tempFilename_ext);
            }


                    if (file_exists($uploadedFile)) {
                        rename($uploadedFile, $updatedFile);
                    }

                // On enregistre le avatar
            $em = $this->getDoctrine()->getManager();
            $avatar->setUrl($tempFilename_ext);
            $avatar->setAlt($tempFilename);
            $avatar->setPositionX(0);
            $avatar->setPositionY(0);

            $avatarWebPath = $this->get('templating.helper.assets')->getUrl($avatar->getWebPath());


            $em->persist($avatar);
            $em->flush();

            return new Response($avatarWebPath);
            /*$return = array(
                "name"=>$blog->getName(),
                "description"=>$blog->getDescription()
            );

            $return=json_encode($return); //jscon encode the array
            return new Response($return,200,array('Content-Type'=>'application/json'));
        */
        }

        return new Response ("Erreur lors de la validation. Réessayez plus tard.");

    }
}
