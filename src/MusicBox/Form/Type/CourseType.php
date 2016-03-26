<?php

namespace MusicBox\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;

class CourseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', 'text', array(
                'constraints' => new Assert\NotBlank(),
                'label'  => 'Titre'
            ))
            ->add('link', 'text', array(
                'required' => FALSE,
                'label'  => 'Lien'
            ))
            ->add('start_at', 'datetime', array(
                'input' => 'datetime',
                'date_format' => 'dd/MM/yyyy',
                'date_widget' => 'single_text',
                'time_widget' => 'choice',
                'label'  => 'Commence à'))
            ->add('end_at', 'datetime', array(
                'input' => 'datetime',
                'date_format' => 'dd/MM/yyyy',
                'date_widget' => 'single_text',
                'time_widget' => 'choice',
                'label'  => 'Fini à'))
            ->add('Enregistrer', 'submit');
    }

    public function getName()
    {
        return 'course';
    }
}
