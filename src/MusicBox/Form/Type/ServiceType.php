<?php

namespace MusicBox\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;

class ServiceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', 'text', array(
                'constraints' => new Assert\NotBlank(),
                'label'  => 'Nom'
            ))
            ->add('description', 'textarea', array(
                'required' => false,
                'label'  => 'Description'
            ))
            ->add('utilisateur', 'text', array(
                'required' => false,
                'label'  => 'Utilisateur'
            ))
            ->add('password', 'text', array(
                'required' => false,
                'label'  => 'Mot de passe'
            ))
            ->add('Enregistrer', 'submit');
    }

    public function getName()
    {
        return 'service';
    }
}
