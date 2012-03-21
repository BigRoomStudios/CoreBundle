<?php

namespace BRS\CoreBundle\Core\Widget;

use BRS\CoreBundle\Core\Utility as util;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\EventDispatcher\Event;

/**
 * SearchFormWidget defines a form for generating filters to apply to lists
 */
class SearchFormWidget extends FormWidget
{
	protected $set_filters;	
		
	protected $filters;
	
	protected $posted = false;
		
	protected $class = 'search-widget';
	
	protected $title = 'Search';
	
	protected $actions = array(
		
		'search' => array(
			'type' => 'button',
		),
		
		'reset' => array(
			'type' => 'button',
			'class' => 'button-grey',
		),
	);
	
	public function handleRequest()
	{	
		$request = $this->getRequest();
			
		$form =& $this->getForm();
		
		

		if ($request->getMethod() == 'POST') {
			
			//util::die_pre($_POST);
			
			if($request->get('reset')){
				
				$this->sessionSet('values', null);
				
				$form->setData(array());
				
				$this->posted = true;
				
				$this->searchEvent();
				
				return true;
				
			}else{
			
				$values = $request->get('form');
				
				if($values){
					
					$this->posted = true;
				}
		
				$this->sessionSet('values', $values);
				
				$form->bindRequest($request);
				
				if ($form->isValid()) {
					
					$this->searchEvent();
					
					return true;
				}
			}
			
		}else{
			
			$this->posted = false;
			
			$values = $this->sessionGet('values');
		
			if($values){
				
				$form->setData($values);
			}
		}
	}
	
	protected function searchEvent(){
		
		$event = new Event();
		
		$this->dispatch('search.post', $event);
	}
	
	public function getFilterFields(){
		
		$fields = $this->getFields();
		
		$values = $this->sessionGet('values');
		
		foreach((array)$values as $field_name => $value){
			
			if(isset($fields[$field_name])){
			
				$fields[$field_name]['value'] = $value;
			}
		}
		
		return $fields;
	}
	
	public function posted(){
		
		return $this->posted;
	}
	
}
	