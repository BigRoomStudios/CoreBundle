<?php

namespace BRS\CoreBundle\Core;

class SuperEntity
{
	
	protected $em;
	
	public function setEntityManager($em){
		
		$this->em = $em;
	}
		
	public function setValues($values){
		
		foreach($values as $key => $value){
					
			$this->setValue($key, $value);
		}	
	}
	
	public function setValue($key, $value){
		
		if(property_exists($this, $key)){
				
			$this->$key = $value;
		}
	}
}