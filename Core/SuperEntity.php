<?php

namespace BRS\CoreBundle\Core;

class SuperEntity
{
		
	function setValues($values){
		
		foreach($values as $key => $value){
					
			if(property_exists($this, $key)){
				
				$this->$key = $value;
			}
		}	
	}
}