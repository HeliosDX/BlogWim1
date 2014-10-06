<?php

namespace Helios\BlogBundle\Form;

use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManager;
use Ratchet\ConnectionInterface;
use Ratchet\Wamp\WampServerInterface;
use Helios\BlogBundle\Entity\Article;

class ArticleHandler
{
    protected $form;
    protected $request;
    protected $em;
    protected $user;

    public function __construct(Form $form, Request $request, EntityManager $em, \Helios\UserBundle\Entity\User $user)
    {
    $this->form = $form;
    $this->request = $request;
    $this->em = $em;
    $this->user = $user;
    }

    public function process()
    {
        // On vérifie qu'elle est de type POST
        if ($this->request->getMethod() == 'POST') {

            // On fait le lien Requête <-> Formulaire
            $this->form->bind($this->request);

            // On vérifie que les valeurs rentrées sont correctes
            if ($this->form->isValid()) {

                $this->onSuccess($this->form->getData());

                return true;
            }
        }

        return false;
    }

    public function onSuccess(Article $article)
    {

        $article->setUser($this->user);

        // On met l'article à la date courante
        $article->setDate(new \DateTime('now'));

        $this->em->persist($article);

        // On fait appel au DataTranformer pour découper la chaine de tags, puis on les enregistre
        //$this->em->getRepository('HeliosBlogBundle:Tag')->findAll();
        foreach($article->getTags() as $tag)
        {
            $this->em->persist($tag);
        }

        /*// --- Début de notre fonctionnalité BigBrother ---
        // On crée l'évènement
        $event = new MessagePostEvent($article->getContenu(), $this->getUser());

        // On déclenche l'évènement
        $this->get('event_dispatcher')
            ->dispatch(BigbrotherEvents::onMessagePost, $event);

        // On récupère ce qui a été modifié par le ou les listener(s), ici le message
        $article->setContenu($event->getMessage());
        // --- Fin de notre fonctionnalité BigBrother ---

        $article->getArticleCompetences()->clear();

        // On enregistre l'objet $article dans la base de données
        $em->persist($article);
        $em->flush();

        foreach ($form->get('articleCompetences')->getData() as $ac) {
            $ac->setArticle($article);
            $em->persist($ac);
        }
        $em->flush();

        // On définit un message flash
        $this->get('session')->getFlashBag()->add('info', 'Article bien ajouté');

        // On redirige vers la page de visualisation de l'article nouvellement créé
        return $this->redirect($this->generateUrl('heliosblog_accueil', array('blog' => $request->get('blog'))));
*/
        $this->em->flush();

    }
}