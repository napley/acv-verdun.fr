<?php

namespace MusicBox\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;

class WidgetType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($builder->getData()->getLocked()) {
            $builder
                    ->add('title', 'text', array(
                    'label' => 'Titre',
                    'attr' => array(
                        'class' => 'hidden'
                    ),
                    'required' => false           
                ))
                ->add('entete', 'text', array(
                    'attr' => array(
                        'class' => 'hidden'
                    ) ,
                    'required' => false            
                ))
                ->add('contenu', 'textarea', array(
                    'attr' => array(
                        'rows' => '15',
                        'class' => 'hidden'
                    ),
                    'required' => false
                ))
                ->add('on_pages', 'checkbox', array(
                    'label'    => 'Afficher sur toutes les pages',
                    'required' => false
                ))
                ->add('on_cats', 'checkbox', array(
                    'label'    => 'Afficher sur toutes les catégories',
                    'required' => false
                ))
                ->add('Enregistrer', 'submit');
        } else {
            $builder
                ->add('title', 'text', array(
                    'constraints' => new Assert\NotBlank(),
                    'label' => 'Titre'
                ))
                ->add('entete', 'text', array(                
                ))
                ->add('contenu', 'textarea', array(
                    'attr' => array(
                        'rows' => '15',
                        'class' => 'editor'
                    )
                ))
                ->add('on_pages', 'checkbox', array(
                    'label'    => 'Afficher sur toutes les pages',
                    'required' => false
                ))
                ->add('on_cats', 'checkbox', array(
                    'label'    => 'Afficher sur toutes les catégories',
                    'required' => false
                ))
                ->add('Enregistrer', 'submit');
        }
    }

    public function getName()
    {
        return 'widget';
    }
}
