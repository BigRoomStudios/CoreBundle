<?php

namespace BRS\CoreBundle\Core\Widget;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\EventDispatcher\Event;

use BRS\CoreBundle\Core\Utility;

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
			'class' => 'btn-primary'
		),
	);
	
	public function &getForm()
	{
		$entity =& $this->getEntity();
		
		$this->form = $this->buildForm();
		
		$entity = $this->getEntity();
		
		$values = (array)$entity;
		
		if($values){
			
			$this->form->setData($values);
		}
		
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
	
	
	
	protected function saveEvent($entity){
		
		$event = new Event();
		
		$event->entity = $entity;
		
		$this->dispatch('edit.save', $event);
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
				
				$em =  $entity->getEntityManager();
				
			    $em->persist($entity);
			    
			    //die();
				
			    $em->flush();
				
				$this->saveEvent($entity);
				
				return $entity->getId();
				
				
	        } else {
	        	
				return false;
				
	        }
	    }
	}
}
	