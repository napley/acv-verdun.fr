<?php

namespace MusicBox\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;

class AlbumPhotoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', 'text', array(
                'constraints' => new Assert\NotBlank(),
                'label'    => 'Titre'
            ))
            ->add('description', 'textarea', array(
                'constraints' => new Assert\NotBlank(),
                'attr' => array(
                    'rows' => '15',
                    'class' => 'editor'
                )
            ))
            ->add('created_at', 'date', array(
                'format' => 'dd/MM/yyyy',
                'widget' => 'single_text',
                'label'    => 'Date',
                'attr' => array(
                    'class' => 'datepicker'
                )
            ))
            ->add('Enregistrer', 'submit');
    }

    public function getName()
    {
        return 'albumPhoto';
    }
}
