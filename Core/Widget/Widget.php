<?php

namespace BRS\CoreBundle\Core\Widget;

use BRS\CoreBundle\Core\Utility;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session;
use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\Event;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * Form defines a base form module and persists session form values in the session
 */
class Widget extends ContainerAware
{
	
	protected $session;

	protected $name;
	
	protected $controller;
	
	protected $entity;
	
	protected $entity_name;
	
	protected $entity_id;	
	
	protected $route_name;
	
	protected $template = 'BRSCoreBundle:Widget:base.html.twig';
	
	protected $class;
	
	protected $title;
	
	protected $listeners = array();
	
	protected $dispatcher;
	

	
	public function __construct($entity_name = null)
	{
		$this->setEntityName($entity_name);
		
		$this->dispatcher = new EventDispatcher();
	}
	
	public function setup(){}
	
	public function setController($controller)
	{
		$this->controller = $controller;
	}

	public function getController()
	{
		return $this->controller;
	}
	
	public function setTitle($title)
	{
		$this->title = $title;
	}

	public function getTitle()
	{
		return $this->title;
	}
	
	public function setName($name)
	{
		$this->name = $name;
	}

	public function getName()
	{
		return $this->name;
	}
	
	public function setClass($class)
	{
		$this->class = $class;
	}

	public function getClass()
	{
		return $this->class;
	}
	
	protected function addWidget($widget, $name){
		
		return $this->controller->addWidget($widget, $name);
	}
	
	public function setEntity($entity)
	{
		$this->entity = $entity;
	}
	
	public function & getEntity()
	{
		if($this->entity){
				
			$em = $this->getDoctrine()->getEntityManager();
			
			$this->entity->setEntityManager($em);
		}
		
		return $this->entity;
	}
	
	public function setEntityName($entity_name)
	{
		$this->entity_name = $entity_name;
	}
	
	public function getEntityName()
	{
		return $this->entity_name;
	}
	
	public function getClassName(){
		
		$class = get_class($this);
		
		return substr(strrchr($class, '\\'),1);
	}
	
	public function dispatch($event_name, $event){
		
		$this->dispatcher->dispatch($event_name, $event);
	}
	
	public function addListener(&$listener, $event_name, $function){
		
		$this->listeners[] = array('listener' => &$listener, 'event' => $event_name);
		
		$this->dispatcher->addListener($event_name, array(&$listener, $function));
	}
	
	public function getListeners(){
		
		return $this->listeners;
	}
	
	public function getListenerIds(){
		
		$ids = array();
		
		foreach($this->listeners as $key => $listener){
			
			$widget = $listener['listener'];
			
			$name = $widget->getName();
			$route = $widget->getRouteName();
			
			$id = $route .'_'. $name;
			
			$ids[$id] = array(
				'name' => $name,
				'route' => $route,
			);
		}
		
		return $ids;
	}
	
	public function getWidgetID(){
		
		$id = $this->route_name . '_' . $this->name;
		
		return $id;
	}
	
	public function getVars()
	{
		$vars = array(
			'name' => $this->name,
			'class' => $this->class,
			'route' => $this->route_name,
			'title' => $this->title,
			'entity_id' => $this->entity_id,
			'widget_class' => $this->getClassName(),
			'widget_id' => $this->getWidgetID(),
			'listeners' => $this->getListenerIds(),
			'max_file_size' => (int)ini_get('upload_max_filesize') * 1024 * 1024,
		);	
					
		return $vars;
	}
	
	/**
	 * Renders a view.
	 *
	 * @param string   $view The view name
	 * @param array	$parameters An array of parameters to pass to the view
	 * @param Response $response A response instance
	 *
	 * @return Response A Response instance
	 */
	public function render()
	{
		$view = $this->template;	
			
		$vars = $this->getVars();
		
		return $this->container->get('templating')->render($view, $vars);
	}
	
	/**
	 * Render this widget and return as plain html or html wrapped in json
	 *
	 * @Route("/render")
	 */
	public function renderAction()
	{	
		$view = $this->template;
		
		$this->handleRequest();
		
		$vars = $this->getVars();
		
		
		if($this->isAjax()){
			
			$values = array(
				'rendered' => $this->container->get('templating')->render($view, $vars)
			);
		
			return $this->jsonResponse($values);
				
		}else{
			
			$response = new Response();
			
			return $this->container->get('templating')->renderResponse($view, $vars, $response);
		}
	}
	
	public function setRouteName($route_name)
	{
		$this->route_name = $route_name;
	}
	
	public function getRouteName()
	{
		return $this->route_name;
	}
	
	public function route($route)
	{
		$params = explode('/', $route);
		
		if($params){
		
			$action = $params[0];
			
			unset($params[0]);
			
			
			/*
			Utility::die_pre($params);
			
			$param_array = array();
				
			foreach($params as $key => $param){
				
				$param_array = '$param[' . $key . ']';
			}
			*/
			
			$param_string = implode(', ', $params);
			
			$action = Utility::to_camel_case($action);
			
			$eval = '$return = $this->' . $action . 'Action(' . $param_string . ');';
			
			eval($eval);
			
			return $return;
		}
	}
	
	
	
	protected function isAjax(){
		
		$request = $this->getRequest();
		
		if ($request->getMethod() == 'GET') {
			
			if($request->query->get('ajax')){
				
				return true;
			}			
	    }
		
		if ($request->getMethod() == 'POST') {
			
			if($request->request->get('ajax')){
				
				return true;
			}			
	    }
	}
	
	public function handleRequest(){}
	
	public function getById($id){
		
		$this->entity_id = $id;
		
		$event = new Event();
		
		$event->id = $id;
		
		$this->dispatch('get.id', $event);
	}
	
	public function onParentGetById($event){
		
		$id = $event->id;
		
		$this->getById($id);
		
		$this->handleRequest();
	}
	
	
	/**
     * Returns true if the service id is defined.
     *
     * @param  string  $id The service id
     *
     * @return Boolean true if the service id is defined, false otherwise
     */
    public function has($id)
    {
        return $this->container->has($id);
    }

    /**
     * Gets a service by id.
     *
     * @param  string $id The service id
     *
     * @return object The service
     */
    public function get($id)
    {
        return $this->container->get($id);
    }
	
	/**
     * Creates and returns a Form instance from the type of the form.
     *
     * @param string|FormTypeInterface $type    The built type of the form
     * @param mixed $data                       The initial data for the form
     * @param array $options                    Options for the form
     *
     * @return Form
     */
    public function createForm($type, $data = null, array $options = array())
    {
        return $this->container->get('form.factory')->create($type, $data, $options);
    }

    /**
     * Creates and returns a form builder instance
     *
     * @param mixed $data               The initial data for the form
     * @param array $options            Options for the form
     *
     * @return FormBuilder
     */
    public function createFormBuilder($data = null, array $options = array())
    {
		$form_factory = $this->container->get('form.factory');
		
        return $form_factory->createBuilder('form', $data, $options);
    }
	
	/**
     * Shortcut to return the Doctrine Registry service.
     *
     * @return Registry
     *
     * @throws \LogicException If DoctrineBundle is not available
     */
    public function getDoctrine()
    {
        if (!$this->container->has('doctrine')) {
            throw new \LogicException('The DoctrineBundle is not installed in your application.');
        }

        return $this->container->get('doctrine');
    }
	
	public function getEntityManager()
    {
        $doctrine = $this->getDoctrine();
		
		return $doctrine->getEntityManager();
    }
	
	/**
     * Get a doctrine repository for a given repository defaults to the primary entity
	 * 
     * @param string  $entity_name       The name of the entity
     * @return array The list fields
     */
	public function getRepository($entity_name = null)
	{		
		$em = $this->getEntityManager();
		
		if($entity_name){
	
       		return $em->getRepository($entity_name);
			
		}else{
		
			return $em->getRepository($this->entity_name);
		}
	}
	
	/**
     * Shortcut to return the request service.
     *
     * @return Request
     */
    public function getRequest()
    {
        return $this->container->get('request');
    }
	
	/**
     * Shortcut to return the session service.
     *
     * @return Session
     */
    public function getSession()
    {
        return $this->getRequest()->getSession();
    }
	
	public function startSession(){}
	
	public function sessionSet($key, $value)
	{
		$session = $this->getSession();
		
		$name = $this->getName();
		
		$route = $this->getRouteName();
		
		$session_name = $route . '_' . $name;
		
		//die($session_name);
		
		$widgets = $session->get('widgets');
		
		if(!$widgets){
			
			$widgets = array();
		}
		
		$widgets[$session_name][$key] = $value;
		
		$session->set('widgets', $widgets);
	}
	
	
	public function sessionGet($key){
		
		$session = $this->getSession();
		
		$name = $this->getName();
		
		$route = $this->getRouteName();
		
		$session_name = $route . '_' . $name;
		
		//die($session_name);
		
		$widgets = $session->get('widgets');
		
		if($widgets && isset($widgets[$session_name][$key])){
			
			return $widgets[$session_name][$key];
		}
	}
	
	public function getActionUrl($action, $absolute = false){
		
		$route = $this->getRouteName();
		
		$name = $this->getName();
		
		$params = array('name' => $name, 'route' => $action);
		
		return $this->generateUrl($route, $params, $absolute);
	}
	
	/**
     * Generates a URL from the given parameters.
     *
     * @param string  $name       The name of the route
     * @param mixed   $parameters An array of parameters
     * @param Boolean $absolute   Whether to generate an absolute URL
     *
     * @return string The generated URL
     */
    public function generateUrl($route, $parameters = array(), $absolute = false)
    {
        return $this->container->get('router')->generate($route, $parameters, $absolute);
    }
	
	/**
     * Forwards the request to another controller.
     *
     * @param  string  $controller The controller name (a string like BlogBundle:Post:index)
     * @param  array   $path       An array of path parameters
     * @param  array   $query      An array of query parameters
     *
     * @return Response A Response instance
     */
    public function forward($controller, array $path = array(), array $query = array())
    {
        return $this->container->get('http_kernel')->forward($controller, $path, $query);
    }

    /**
     * Returns a RedirectResponse to the given URL.
     *
     * @param string  $url The URL to redirect to
     * @param integer $status The status code to use for the Response
     *
     * @return RedirectResponse
     */
    public function redirect($url, $status = 302)
    {
        return new RedirectResponse($url, $status);
    }
	
	protected function jsonResponse($values){
		
		return new Response(json_encode($values));
	}
	
	protected function getUser(){
		
		$securityContext = $this->get('security.context');
		$token = $securityContext->getToken();
		$user = $token->getUser();
		
		return $user;
	}
	
}