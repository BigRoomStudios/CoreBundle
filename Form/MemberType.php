<?php

namespace BRS\CoreBundle\Form;

use BRS\CoreBundle\Core\Form\Form;
use Symfony\Component\Form\FormBuilder;

class MemberForm extends Form
{
    
	public function __construct($session){
		
		parent::__construct($session, 'brs_corebundle_membertype');
		
		$this->setFields(array(
			'email' => null,
			'first_name' => null,
			'last_name' => null,
		));
	}
}
