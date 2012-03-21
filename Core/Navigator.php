<?php

namespace BRS\CoreBundle\Core;


/**
 * Navigator manages a hierarchical navigation scheme
 */
class Navigator
{
	protected $nav;
	protected $selected;
	
	public function __construct($nav){
		
		$this->nav = $nav;
	}
	
	public function getNav(){
		
		return $this->nav;
	}
	
	public function setSelected($route){
		
		$this->selected = $route;
		
		$this->doSelect($this->nav, $route);
		
		//Utility::die_pre($this->nav);
	}
	
	protected function doSelect(&$nav, $route){
		
		foreach ($nav as $key => $page) {
			
			if(isset($page['pages'])){
				
				if($this->doSelect($nav[$key]['pages'], $route)){
					
					$nav[$key]['selected'] = true;
				
					return true;
				}
					
			}else if(isset($page['route']) && $page['route'] == $route){
				
				$nav[$key]['selected'] = true;
				
				return true;
			}
		}
		
	}
	
	
}
	