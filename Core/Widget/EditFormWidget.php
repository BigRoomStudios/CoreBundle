<?php

namespace BRS\CoreBundle\Core\Widget;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

/**
 * editFormWidget defines a basic form for manipulating entity data
 */
class EditFormWidget extends FormWidget
{

	protected $class = 'edit-widget';
	
	protected $title = 'Edit';
	
	protected $actions = array(
		
		'save' => array(
			'type' => 'button',
		),
	);
	
	public function &getForm()
	{
		//if(!isset($this->form)){
			
			$entity =& $this->getEntity();
			
			$this->form = $this->buildForm();
			
			$entity = $this->getEntity();
			
			//$session_values = (array)$this->sessionGet('values');
			
			//Utility::die_pre($session_values);
			
			//$values = array_merge((array)$entity, $session_values);
			
			//$values['first_name'] = $entity->getFirstName();
			
			$values = (array)$entity;
			
			if($values){
				
				$this->form->setData($values);
			}
			
			//$this->form->setName($this->getName());
		//}
		
		return $this->form;
	}
	
	public function getRedirect($id){
	
		$success_route = $this->getSuccessRoute();
		
		if($id && $success_route){
			
			$return = array(
				'route' => $success_route,
				'url' => $this->generateUrl($success_route, array('id' => $id))
			);
			
			return $return;
		}
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
	