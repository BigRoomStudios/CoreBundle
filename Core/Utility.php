<?php

namespace BRS\CoreBundle\Core;

use Symfony\Bundle\DoctrineBundle\Registry;
use Symfony\Component\Form\Form;

/**
 * Utility is a collection of generic handy functions
 */
class Utility
{
	static function die_pre($value, $max = 2)
	{	
		Utility::print_pre($value, $max);
		die();
	}
	
	static function print_pre($value, $max = 2)
	{
		if(is_array($value) || is_object($value))
			$value = self::toArray($value, $max);
		
		print('<pre>');
		
		print_r($value);
		
		print('</pre>');
	}
	
	static function die_dump($value)
	{
		Utility::pre_dump($value);
		die();
	}
	
	static function pre_dump($value)
	{
		print('<pre>');
	
		var_dump($value);
	
		print('</pre>');
	}
	
	static function toArray($object, $max = 2, $current = 0)
	{
		if($current > $max)
			return ucfirst(gettype($object)).'( ... )';
		
		$current++;
		$return = array();
		foreach((array) $object as $key => $data)
			if (is_object($data))
				$return[$key] = self::toArray($data, $max, $current);
			elseif (is_array($data))
				$return[$key] = self::toArray($data, $max, $current);
			else
				$return[$key] = $data;
			
		return $return;
	}
	
	/**
	 * Translates a camel case string into a string with underscores (e.g. firstName -&gt; first_name)
	 * @param		string	 $str		String in camel case format
	 * @return		string						$str Translated into underscore format
	 */
	static function from_camel_case($str) {
		$str[0] = strtolower($str[0]);
		$func = create_function('$c', 'return "_" . strtolower($c[1]);');
		return preg_replace_callback('/([A-Z])/', $func, $str);
	}
 
	/**
	 * Translates a string with underscores into camel case (e.g. first_name -&gt; firstName)
	 * @param		string	 $str										 String in underscore format
	 * @param		bool		 $capitalise_first_char	 If true, capitalise the first char in $str
	 * @return	 string															$str translated into camel caps
	 */
	static function to_camel_case($str, $capitalise_first_char = false) {
		if($capitalise_first_char) {
			$str[0] = strtoupper($str[0]);
		}
		$func = create_function('$c', 'return strtoupper($c[1]);');
		return preg_replace_callback('/_([a-z])/', $func, $str);
	}
	
	static function get_form_errors(\Symfony\Component\Form\Form $form) {
		
	    $errors = array();
	    
	    foreach ($form->getErrors() as $key => $error) {
	        
			$template = $error->getMessageTemplate();
			$parameters = $error->getMessageParameters();
			
			foreach($parameters as $var => $value){
				
				$template = str_replace($var, $value, $template);
			}
			
	        $errors[$key] = $template;
	    }
		
	    if ($form->hasChildren()) {
	        foreach ($form->getChildren() as $child) {
	            if (!$child->isValid()) {
	                $errors[$child->getName()] = $this->getErrorMessages($child);
	            }
	        }
	    }
	
	    return $errors;
	}
	
}	