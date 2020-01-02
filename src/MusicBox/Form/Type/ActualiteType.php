<?php

namespace MusicBox\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

class ActualiteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', 'text', array(
                'constraints' => new Assert\NotBlank(),
                'label'  => 'Titre'
            ))
            ->add('contenu', 'textarea', array(
                'attr' => array(
                    'rows' => '15',
                    'class' => 'editor'
                )
            ))
            ->add('affichage', 'checkbox', array(
                'label'    => 'Afficher cette Article',
                'required' => false
            ))
            ->add('home', 'checkbox', array(
                'label'    => 'Afficher sur la page d\'Accueil',
                'required' => false
            ))
            ->add('Enregistrer', 'submit');
    }

    public function getName()
    {
        return 'actualite';
    }
}
