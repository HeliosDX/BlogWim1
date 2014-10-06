<?php

namespace Helios\ManagerBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class BlogType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('user', new \Helios\UserBundle\Form\RegistrationUserFormType('\Helios\UserBundle\Entity\User'))
            //->add('avatar', new \Helios\BlogBundle\Form\AvatarType('\Helios\BlogBundle\Entity\Avatar'))
            /*->add('meta')
            ->add('date', 'date' ,array(
                'widget' => 'single_text',
                'input' => 'datetime',
                'format' => 'dd-MM-yyyy',
                'attr' => array('class' => 'date'),
                'data' => new \DateTime('now')
            ))*/
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Helios\ManagerBundle\Entity\Blog'
        ));
    }

    public function getName()
    {
        return 'helios_managerbundle_blogtype';
    }
}
