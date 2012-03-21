<?php

namespace BRS\CoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class Test2Type extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
            ->add('tes2')
        ;
    }

    public function getName()
    {
        return 'brs_corebundle_test2type';
    }
}
