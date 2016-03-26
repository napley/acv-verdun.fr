<?php

namespace MusicBox\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;

class EntrainementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', 'text', array(
                'constraints' => new Assert\NotBlank(),
                'label' => 'Titre'
            ))
            ->add('script', 'textarea', array(
                'constraints' => new Assert\NotBlank(),
                'attr' => array(
                    'rows' => '5',
                    'class' => 'script_entrainement'
                ),
                'label' => 'Script'
            ))
            ->add('Enregistrer', 'submit');
    }

    public function getName()
    {
        return 'entrainement';
    }
}
