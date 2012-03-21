<?php

namespace BRS\CoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class TestType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
            ->add('test1')
        ;
    }

    public function getName()
    {
        return 'brs_corebundle_testtype';
    }
}
