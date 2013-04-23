<?php

namespace BRS\CoreBundle\Core;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Util\Debug;

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
		
		return $this->em;
	}
		
	public function setValues($values){
		
		foreach($values as $key => $value)
			$this->setValue($key, $value);
		
	}
	
	public function setValue($key, $value){
		
		$setter = Utility::to_camel_case('set_'.$key);
		
		if(method_exists($this, $setter))
			$this->$setter($value);
		elseif(property_exists($this, $key))
			$this->$key = $value;
		
	}
}