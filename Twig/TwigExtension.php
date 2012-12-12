<?php

namespace BRS\CoreBundle\Twig;

class TwigExtension extends \Twig_Extension {
	
	public function getName(){
		
        return 'brs_twig_extension';
    }
	
    public function getFilters() {
    	
        return array(
            'var_dump'   => new \Twig_Filter_Function('var_dump'),
            'highlight'  => new \Twig_Filter_Method($this, 'highlight'),
            'print_pre'  => new \Twig_Filter_Method($this, 'print_pre'),
            'file_size'  => new \Twig_Filter_Method($this, 'file_size'),
        );
    }
	
	public function getFunctions(){
		
        return array(
            'update'  => new \Twig_Function_Method($this, 'update'),
        );
    }
	
	public function getTests(){
		
        return array(
            'numeric' => new \Twig_Test_Method($this, 'numeric'),
        );
    }
	
	public function update($object, $key, $value){
		
		$object[$key] = $value;
		
		return $object;		
	}
	
    public function highlight($sentence, $expr) {
    	
        return preg_replace('/(' . $expr . ')/', '<span style="color:red">\1</span>', $sentence);
    }
	
    public function print_pre($object) {
        
		$print = print_r($object, true);	
			
        return '<pre>' . $print . '</pre>';
    }

	public function numeric($value){
		
		return is_numeric($value);
	}

	public function file_size($value){
		
		if ($value >= 1073741824) { return round($value / 1073741824, 2) . ' GB'; }
		if ($value >= 1048576)    { return round($value / 1048576, 2) . ' MB'; }
		if ($value >= 1024)       { return round($value / 1024, 0) . ' KB'; }
		return $value . ' B';
	}
}