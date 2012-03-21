<?php

namespace BRS\CoreBundle\Core\Widget;

use BRS\CoreBundle\Core\Utility as brs;


/**
 * Form defines a base form module and persists session form values in the session
 */
class PanelWidget extends Widget
{		
	protected $widgets;
	
	protected $template = 'BRSCoreBundle:Widget:panel.html.twig';
	
	public function __construct()
	{
		parent::__construct();
	}
	
	public function setWidgets($widgets)
	{
		$this->widgets = $widgets;
	}
	
	public function getWidgets()
	{
		return $this->widgets;
	}
	
	public function getRenderedWidgets(){
		
		$widgets = $this->getWidgets();
		
		$rendered = array();
		
		foreach ((array)$widgets as $key => $widget) {
			
			$rendered[$key] = $widget->render();
		}
		
		return $rendered;
	}
	
	public function getVars(){
		
		$add_vars = array(
			'widgets' => $this->getRenderedWidgets(),
		);
		
		$vars = array_merge(parent::getVars(), $add_vars);
		
		return $vars;
	}
}
	