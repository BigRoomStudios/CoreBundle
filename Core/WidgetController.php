<?php

namespace BRS\CoreBundle\Core;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;


/**
 * WidgetController
 * base controller for handling a collection of "views", each view containing a collection of "widgets"
 * each widget acts as a micro MVC system within the "page" created by a WidgetController
 */
class WidgetController extends Controller
{
	
	protected $route_name;
	protected $entity;
	protected $entity_name;
	protected $widgets = array();
	protected $widget_factory;
	protected $views = array();
	protected $view;
	
	/**
	 * Passes widget routes on to widget controllers
	 *
	 * @Route("/widget/{name}/{route}", requirements={"route" = ".*"})
	 * @Template("BRSCoreBundle:Widget:base.html.twig")
	 */
	public function widgetAction($name, $route = 'render')
	{
		$widget = $this->getWidget($name);
					
		if($widget){
			
			$return = $widget->route($route);
			
			return $return;
			
			//return $return;
		}else{
			
			throw new \Exception('No registered widget with that name: ' . $name);
		}
			
	}
	
	public function setContainer(ContainerInterface $container = null){
				
		parent::setContainer($container);
		
		$this->widget_factory = $this->get('widget_factory');
		
		$this->widget_factory->setContainer($container);
		
		$this->setup();
	}
	
	protected function setup(){}
	
	public function addWidget($widget, $name){
		
		$widget = $this->buildWidget($widget, $name);
		
		$this->registerWidget($widget);
		
		return $widget;
	}
	
	protected function buildWidget($widget, $name){
		
		$widget = $this->widget_factory->buildWidget($widget, $name, $this);
				
		$widget->setRouteName($this->getRouteName() . '_widget');
		
		if(!$widget->getEntityName()){
		
			$widget->setEntityName($this->entity_name);
		}
		
		if(!$widget->getEntity()){
			
			$widget->setEntity($this->getEntity());
		}
		
		$widget->startSession();
		
		return $widget;
	}
	
	protected function registerWidget($widget)
	{
		$name = $widget->getName();
		
		$this->widgets[$name] = $widget;
	}
	
	protected function getWidget($name)
	{
		if(isset($this->widgets[$name])){
		
			return $this->widgets[$name];
		}
	}
	
	protected function setView($name)
	{
		$this->view = $name;
	}
	
	protected function addView($name, $widget)
	{
		$this->views[$name] = $widget;
	}
	
	protected function getView($name)
	{
		return $this->views[$name];
	}
	
	protected function setRouteName($route_name)
	{
		$this->route_name = $route_name;
	}
	
	protected function getRouteName()
	{
		return $this->route_name;
	}
	
	/**
     * Sets the doctrine entity name of the primary object
     *
     * @param string  $entity_name       The name of the entity
     */
	public function setEntityName($entity_name)
	{	
		$this->entity_name = $entity_name;
	}
	
	public function setEntity($entity)
	{
		$this->entity = $entity;
	}
	
	public function &getEntity()
	{
		return $this->entity;
	}
	
	/**
     * Get a doctrine repository for a given repository defaults to the primary entity
	 * 
     * @param string  $entity_name       The name of the entity
     * @return array The list fields
     */
	public function getRepository($entity_name = null)
	{		
		$em = $this->getDoctrine()->getEntityManager();
		
		if($entity_name){
	
       		return $em->getRepository($entity_name);
			
		}else{
		
			return $em->getRepository($this->entity_name);
		}
	}
	
	public function getEntityManager(){
		
		$em = $this->getDoctrine()->getEntityManager();
		
		return $em;
	}
	
	protected function isAjax(){
		
		$request = $this->getRequest();
		
		if ($request->getMethod() == 'GET') {
			
			if($request->query->get('ajax')){
				
				return true;
			}			
	    }
	}
	
	protected function jsonResponse($values){
		
		return new Response(json_encode($values));
	}
	
	private function getErrorMessages(\Symfony\Component\Form\Form $form) {
		
	    $errors = Utility::get_form_errors($form);
	
	    return $errors;
	}
	
	public function getUser(){
		
		$securityContext = $this->get('security.context');
		$token = $securityContext->getToken();
		$user = $token->getUser();
		
		return $user;
	}
	
}