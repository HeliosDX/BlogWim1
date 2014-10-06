<?php

namespace Helios\UserBundle\Form;

use FOS\UserBundle\Form\Type\RegistrationFormType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class RegistrationUserFormType extends RegistrationFormType // Ici, on hérite de RegistrationFormType de FOSUserBundle
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // On fait appel à la méthode buildForm du parent, qui va ajouter tous les champs à $builder
        parent::buildForm($builder, $options);
    }
}
