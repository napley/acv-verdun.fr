<?php

namespace MusicBox\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;

class InfoSiteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', 'text', array(
                'constraints' => new Assert\NotBlank(),
                'label'  => 'Nom'
            ))
            ->add('description', 'text', array(
                'label'  => 'Description',
                'required' => false
            ))
            ->add('valeur', 'textarea', array(
                'required' => false
            ))
            ->add('img', 'checkbox', array(
                'label'    => 'Type image ?',
                'required' => false,
            ))
            ->add('Enregistrer', 'submit');
    }

    public function getName()
    {
        return 'infosite';
    }
}
