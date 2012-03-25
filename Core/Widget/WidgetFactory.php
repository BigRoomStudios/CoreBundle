<?php

namespace BRS\CoreBundle\Core\Widget;

use BRS\CoreBundle\Core\Widget\Widget;
use Symfony\Component\DependencyInjection\ContainerAware;

/**
 * Form defines a base form module and persists session form values in the session
 */
class WidgetFactory extends ContainerAware
{

	public function buildWidget($widget = null, $name, $controller)
	{
		
		if(!$widget){
			
			$widget = new Widget();
		}
		
		
		$widget->setController($controller);
		$widget->setContainer($this->container);
		$widget->setName($name);
		$widget->setup();
		
		return $widget;
	}
	
	
}