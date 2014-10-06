<?php
// src/Helios/UserBundle/Controller/RegistrationController.php;

namespace Helios\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\SecurityContext;
use FOS\UserBundle\Controller\ChangePasswordController as BaseController;

class ChangePasswordController extends BaseController
{
    protected function renderChangePassword(array $data)
    {
        $view = 'changePassword_contenjt';

        $template = sprintf('FOSUserBundle:ChangePassword:%s.html.%s', $view, $this->container->getParameter('fos_user.template.engine'));

        return $this->container->get('templating')->renderResponse($template, $data);
    }
}
