<?php

namespace BRS\CoreBundle\Core;

use Symfony\Bundle\DoctrineBundle\Registry;
use Symfony\Component\Form\Form;

/**
 * Utility is a collection of generic handy functions
 */
class Utility
{
	
	/**
	 * Custom print_r() that calles self::print_pre($value) and dies PHP
	 * @param		mixed			$value		Value to print into a pre on client-side
	 * @return		bool						Success or failure
	 */
	static function die_pre($value, $max = 2) {	
		if (Utility::print_pre($value, $max))
			die();
		else return false;
	}
	
	/**
	 * Custom print_r() that prints $value inside a <pre> element for legibilty
	 * @param		mixed			$value		Value to print into a pre on client-side
	 * @return		bool						Success or failure
	 */
	static function print_pre($value, $max = 2) {
		if(is_array($value) || is_object($value))
			$value = self::toArray($value, $max);
		
		return print_r("<pre>\n".$value."\n</pre>");
	}
	
	/**
	 * Custom var_dump() that calles self::pre_dump($value) and dies PHP
	 * @param		mixed			$value		Value to dump into a pre on client-side
	 * @return		bool						Success or failure
	 */
	static function die_dump($value) {
		if (self::pre_dump($value))
			die();
		else return false;
	}
	
	/**
	 * Custom var_dump() that dumps $value inside a <pre> element for legibilty
	 * @param		mixed			$value		Value to dump into a pre on client-side
	 * @return		bool						Success or failure
	 */
	static function pre_dump($value) {
		return self::print_pre(var_dump($value,true));
	}
	
	/**
	 * Custom error_log() that sanitizes inputs and provides easy to see headers
	 * @param		mixed			$message	A standalone message, data to be displayed, or header for data
	 * @param		mixed			$data		Additional data to be applied after $message
	 * @param		number			$max		Maximum number of levels to traverse into $message or $data if they are arrays or objects
	 * @return		bool						Success or failure
	 */
	static function log($message, $data=null, $max=2) {
		
		$message = self::toString($message,$max);
		$data = ($data) ? self::toString($data,$max) : null;
		
		if(!empty($data)) $data = "\n".$data;
		
		if (!strstr($message, PHP_EOL))
			$message = str_pad(" ".$message." ", 80,'=',STR_PAD_BOTH);
	
		return error_log($message.$data);
	}
	
	/**
	 * Sends all ASCI Characters from Dec[0] to Dec[255] to the log to test sanitization
	 */
	static function testHex() {
		$chars = array();
		for($i = 0; $i <= 255; $i++)
			$chars[$i] =chr($i);
		
		self::log('All ASCI Characters from Dec[0] to Dec[255]',$chars);
	}
	
	/**
	 * Returns a sanitized string without non-printable characters
	 * @param		string			$data		A string to be sanitized
	 * @return		string						$data without non-printable characters
	 */
	static function removeControlChars($data) {
		// Strict, but safe.
		$test = '/[^\x0A\x20-\x7E\xC0-\xD6\xD8-\xF6\xF8-\xFF]/';
		
		// Extended, but buggy.
		// $test = '/(?!\n)[[:cntrl:]]+/';
		
		return preg_replace($test,' ',$data);
	}
	
	/**
	 * Returns a string representation of whatever is passed to it
	 * @param		mixed			$data		Any type of variable
	 * @param		number			$max		Maximum levels to go into $data if it is traversable
	 * @return		string						$data as a string
	 */
	static function toString($data, $max=2) {
		return self::removeControlChars(print_r(self::toArray($data, $max),true));
	}
	
	/**
	 * Translates an Object or Array on N levels to an Array of $max levels.
	 * @param		mixed			$data		Object or Array to process
	 * @param		number			$max		Maximum levels to go into $object before truncating
	 * @param		number			$current	Current depth of $object as this is self-referencing
	 * @return		string/array				$object as a string or array.
	 */
	static function toArray($data, $max = 2, $current = 0, $key=null) {
		
		$current++;
		$return = array();

		if (!is_scalar($data) && !is_null($data)){
			if($current > $max)
				return ucfirst(gettype($data)).'( '.((empty($data)) ? 'EMPTY' : '...').' )';
			else
				foreach((array) $data as $key => $object)
					 $return['{'.gettype($object).'}'.$key] = self::toArray($object, $max, $current, $key);
		}
		elseif (is_bool($data))
			return ($data) ? 'TRUE' : 'FALSE';
		elseif (is_null($data))
			return 'NULL';
		else return $data;
		
		
		
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
		if($capitalise_first_char)
			$str[0] = strtoupper($str[0]);
		
		$func = create_function('$c', 'return strtoupper($c[1]);');
		return preg_replace_callback('/_([a-z])/', $func, $str);
	}
	
	static function get_form_errors(\Symfony\Component\Form\Form $form) {
		
	    $errors = array();
	    
	    foreach ($form->getErrors() as $key => $error) {
	        
			$template = $error->getMessageTemplate();
			$parameters = $error->getMessageParameters();
			
			foreach($parameters as $var => $value)
				$template = str_replace($var, $value, $template);
			
	        $errors[$key] = $template;
	    }
		
	    if ($form->hasChildren())
	    	foreach ($form->getChildren() as $child)
	            if (!$child->isValid())
	                $errors[$child->getName()] = $this->getErrorMessages($child);
	
	    return $errors;
	}
	
}	