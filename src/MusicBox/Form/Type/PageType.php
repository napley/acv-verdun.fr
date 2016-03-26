<?php

namespace MusicBox\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;

class PageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', 'text', array(
                'constraints' => new Assert\NotBlank(),
                'label' => 'Titre'
            ))
            ->add('description', 'textarea', array(
                'attr' => array(
                    'rows' => '7',
                    'class' => 'editor'
                )
            ))
            ->add('contenu', 'textarea', array(
                'attr' => array(
                    'rows' => '15',
                    'class' => 'editor'
                )
            ))
            ->add('Enregistrer', 'submit');
    }

    public function getName()
    {
        return 'page';
    }
}
