<?php

namespace BRS\CoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class PageType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
            ->add('title')
            ->add('description')
            ->add('route')
            ->add('date_added')
            ->add('date_modified')
        ;
    }

    public function getName()
    {
        return 'brs_corebundle_pagetype';
    }
}
