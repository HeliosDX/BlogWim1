<?php
// src/Helios/UserBundle/Controller/RegistrationController.php;

namespace Helios\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\SecurityContext;
use FOS\UserBundle\Controller\RegistrationController as BaseController;

class RegistrationController extends BaseController
{
    protected function renderRegister(array $data)
    {
        $view = 'registration_content';

        $template = sprintf('FOSUserBundle:Registration:%s.html.%s', $view, $this->container->getParameter('fos_user.template.engine'));

        return $this->container->get('templating')->renderResponse($template, $data);
    }
}
