<?php

namespace MusicBox\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;

class DateAlbumPhotoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('created', 'date', array(
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
        return 'dateAlbumPhoto';
    }
}
