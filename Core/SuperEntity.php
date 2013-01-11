<?php

namespace BRS\CoreBundle\Core;

use Doctrine\ORM\Mapping as ORM;

/*
 * @ORM\MappedSuperclass
 * @ORM\HasLifecycleCallbacks()
 */
class SuperEntity
{
	
	protected $em;
	
	public function setEntityManager($em){
		
		$this->em = $em;
	}
	
	public function getEntityManager(){
		
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