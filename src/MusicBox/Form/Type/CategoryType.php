<?php

namespace MusicBox\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;

class CategoryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', 'text', array(
                'constraints' => new Assert\NotBlank(),
                'label'  => 'Titre'
            ))
            ->add('abrev', 'text', array(
                'constraints' => new Assert\NotBlank(),
            ))
            ->add('Enregistrer', 'submit');
    }

    public function getName()
    {
        return 'category';
    }
}
