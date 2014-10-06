<?php
// src/Acme/HelloBundle/Security/Firewall/ExceptionListener.php
namespace Helios\BlogBundle\Security\Firewall;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session;
use Symfony\Component\Security\Http\Firewall\ExceptionListener as BaseExceptionListener;

class ExceptionListener extends BaseExceptionListener
{
    protected function setTargetPath(Request $request)
    {
        // Ne conservez pas de chemin cible pour les requêtes XHR et non-GET
        // Vous pouvez ajouter n'importe quelle logique supplémentaire ici
        // si vous le voulez
        if ($request->isXmlHttpRequest() || 'GET' !== $request->getMethod()) {
            return;
        }

        $session = $request->getSession();

        $session->set('_security.target_path', $request->getUri());
        $session->getFlashBag()->add('info',$request->getUri());

        return $this->redirect($this->generateUrl('heliosblog_accueil', array('blog' => $request->get('blog'))));
    }
}