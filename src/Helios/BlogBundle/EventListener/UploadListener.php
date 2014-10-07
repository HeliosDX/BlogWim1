<?php
namespace Helios\BlogBundle\EventListener;

use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpFoundation\Response;
use Helios\BlogBundle\Entity\Avatar;
use Oneup\UploaderBundle\Event\PostUploadEvent;

class UploadListener
{
    public function __construct($doctrine)
    {
        $this->doctrine = $doctrine;
    }

    // Méthode pour ajouter le fichier en bdd
    public function onUpload(PostUploadEvent $event)
    {
        $response = $event->getResponse();

        $file       = $event->getFile();

        /*$avatar = new Avatar();
        $array = explode(".",$file->getFileName());
        //$avatar->setId($array[0]);
        $avatar->setUrl($array[1]);
        $avatar->setAlt($file->getFileName());

        $em = $this->doctrine->getManager();
        $em->persist($avatar);
        $em->flush();*/

        $response['name'] = $file->getFileName();
        //return $this->redirect($this->generateUrl('heliosblog_accueil', array('blog' => $request->get('blog'))));
    }
}