<?php

namespace BRS\CoreBundle\Core\Widget;

use BRS\CoreBundle\Core\Utility as brs;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session;
use Symfony\Component\Form\FormBuilder;

/**
 * Form defines a base form module and persists session form values in the session
 */
class FormWidget extends Widget
{
	protected $session;
	
	protected $fields;
	
	protected $actions;		
		
	protected $values;
	
	protected $template = 'BRSCoreBundle:Widget:form.html.twig';
	
	protected $form;
	
	public $success_route;
	
	public function __construct()
	{
		parent::__construct();
	}
	
	public function setFields($fields)
	{
		$this->fields = $fields;
	}
	
	public function getFields()
	{
		return $this->fields;
	}
	
	public function setActions($actions)
	{
		$this->actions = $actions;
	}
	
	public function getActions()
	{
		return $this->actions;
	}
	
	public function setSuccessRoute($route)
	{
		$this->success_route = $route;
	}
	
	public function getSuccessRoute()
	{
		return $this->success_route;
	}

	public function buildForm(&$data = null)
	{
		$builder = $this->createFormBuilder($data);
		
		if($this->fields){
						
			$this->buildFormFields($builder, $this->fields);
		}
		
		return $builder->getForm();
	}
	
	protected function buildFormFields(&$builder, $fields){
		
		foreach($fields as $field_name => $field){
				
			// set feild type var, then remove from options
			$type = $field['type'];
			
			if($type == 'group'){
			
				$this->buildFormFields($builder, $field['fields']);
				
			}else{
				
				$options = array();
				
				if(isset($field['required'])){
					
					$options['required'] = $field['required'];
				}	
				
				if(isset($field['attr'])){
					
					$options['attr'] = $field['attr'];
				}	
				
				$builder->add($field_name,$type,$options);
			}
		}		
	}
	
	public function &getForm()
	{
		if(!isset($this->form)){
			
			$this->form = $this->buildForm();
			
			//$this->form->setName($this->getName());
		}
		
		return $this->form;
	}
	
	public function getById($id){
		
		//die($id);
		
		$this->entity_id = $id;
		
		$entity = $this->getRepository()->find($id);
		
		if($entity){
			
			$this->setEntity($entity);
		}
	}
	
	public function getRedirect($success){
	
		$success_route = $this->getSuccessRoute();
		
		if($success && $success_route){
			
			$return = array(
				'route' => $success_route,
				'url' => $this->generateUrl($success_route)
			);
			
			return $return;
		}
	}
	
	public function getVars()
	{	
		$success = $this->handleRequest();
		
		$form =& $this->getForm();
		
		$actions = $this->getActions();
				
		$redirect = $this->getRedirect($success);
		
		$add_vars = array(
			'values' => $this->values,
			'fields' => $this->fields,
			'form' => $form->createView(),
			'actions' => $actions,
			'success' => $success,
			'redirect' => $redirect,
			'entity_id' => $this->entity_id,
		);
		
		$vars = array_merge(parent::getVars(), $add_vars);
		
		return $vars;
	}
	
	
	public function handleRequest()
	{
			
		$request = $this->getRequest();
			
		$form =& $this->getForm();
		
		if ($request->getMethod() == 'POST') {
				
			$form->bindRequest($request);
			
	        if ($form->isValid()) {
	            
				return true;
				
	        } else {
	        	
				return false;
	        }
	    }
	}
	
	/**
	 * Displays a form to create a new entity for this admin module
	 *
	 * @Route("/post")
	 */
	public function postAction()
	{
		$request = $this->getRequest();	
			
		$id = $request->query->get('id');
		
		if($id){
			
			$this->getById($id);
		}
				
		$vars = $this->getVars();
		
		if($this->isAjax()){
		
			return $this->jsonResponse($vars);
				
		}else{
			
			$view = $this->template;
			
			$response = new Response();
			
			return $this->container->get('templating')->renderResponse($view, $vars, $response);
		}
		
	}
	
}
	