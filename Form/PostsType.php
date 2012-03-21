<?php

namespace BRS\CoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class PostsType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
            ->add('title')
            ->add('body')
            ->add('date')
        ;
    }

    public function getName()
    {
        return 'brs_corebundle_poststype';
    }
}
