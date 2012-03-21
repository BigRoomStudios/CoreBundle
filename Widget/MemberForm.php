<?php

namespace BRS\CoreBundle\Widget;

use BRS\CoreBundle\Core\Widget\EditFormWidget;
use BRS\CoreBundle\Core\Widget\FormWidget;
use BRS\CoreBundle\Core\Utility;

/**
 * Member form widget
 */
class MemberForm extends EditFormWidget
{
	public function __construct()
	{
		parent::__construct();	
		
		$this->setEntityName('BRSCoreBundle:Member');
		
		$edit_fields = array(
			
			'info' => array(
			
				'type' => 'group',
				'fields' => array(
			
					'first_name' => array(
						'type' => 'text',
						'required' => true,
						'attr' => array(
							'class' => 'valid-alpha'
						)
					),
					
					'last_name' => array(
						'type' => 'text',
						'required' => true,
						'attr' => array(
							'class' => 'valid-alpha'
						)
					),
					
					'email' => array(
						'type' => 'text',
						'required' => true,
						'attr' => array(
							'class' => 'valid-email'
						),
					),
				),
			),
			
			'password' => array(
			
				'type' => 'group',
				'fields' => array(
			
					'new_password' => array(
						'type' => 'text',
						'required' => false,
						'attr' => array(
							'class' => 'valid-alpha'
						),
					),
				),
			),	
		);
		
		/*$edit_fields = array(
			
			'first_name' => array(
				'type' => 'text',
				'required' => true,
				'attr' => array(
					'class' => 'valid-alpha'
				)
			),
			
			'last_name' => array(
				'type' => 'text',
				'required' => true,
				'attr' => array(
					'class' => 'valid-alpha'
				)
			),
			
			'email' => array(
				'type' => 'text',
				'required' => true,
				'attr' => array(
					'class' => 'valid-email'
				),
			),

			'new_password' => array(
				'type' => 'text',
				'required' => false,
				'attr' => array(
					'class' => 'valid-alpha'
				),
			),
				
		);*/
		
		$this->setFields($edit_fields);
	}
	
	
	public function handleRequest()
	{
		$request = $this->getRequest();
		
		if ($request->getMethod() == 'POST') {
			
			$values = $request->get('form');
			
			$form =& $this->getForm();
			
			$form->bindRequest($request);
			
	        if ($form->isValid()) {
	            	
				$entity =& $this->getEntity();
				
				$entity->setValues($values);
				
				$new_password = $values['new_password'];
				
				if($new_password){
				
					$factory = $this->get('security.encoder_factory');
					
					$encoder = $factory->getEncoder($entity);
					
					$password = $encoder->encodePassword($new_password, $entity->getSalt());
					
					$entity->setPassword($password);
				}
				
				$em = $this->getDoctrine()->getEntityManager();
				
			    $em->persist($entity);
				
			    $em->flush();
				
				return $entity->getId();
				
	        } else {
	        	
				return false;
				
	        }
	    }
	}
	
}
	