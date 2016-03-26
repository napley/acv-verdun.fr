<?php

namespace MusicBox\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', 'text', array(
                'constraints' => new Assert\NotBlank(),
                'label' => 'Identifiant'
            ))
            ->add('nom', 'text', array(
                'constraints' => new Assert\NotBlank(),
            ))
            ->add('prenom', 'text', array(
                'constraints' => new Assert\NotBlank(),
            ))
            ->add('password', 'repeated', array(
                'type'            => 'password',
                'invalid_message' => 'Les mots de passe doivent être identique.',
                'options'         => array('required' => false),
                'first_options'   => array('label' => 'Mot de passe'),
                'second_options'  => array('label' => 'Confirmation du mot de passe'),
                'required' => FALSE,
            ))
            ->add('role', 'choice', array(
                'choices' => array('ROLE_USER' => 'User', 'ROLE_ADMIN' => 'Admin')
            ))
            ->add('Enregistrer', 'submit');
    }

    public function getName()
    {
        return 'user';
    }
}
