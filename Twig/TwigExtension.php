<?php

namespace BRS\CoreBundle\Twig;

class TwigExtension extends \Twig_Extension {

    public function getFilters() {
        return array(
            'var_dump'   => new \Twig_Filter_Function('var_dump'),
            'highlight'  => new \Twig_Filter_Method($this, 'highlight'),
            'print_pre'  => new \Twig_Filter_Method($this, 'print_pre'),
        );
    }
	
	public function getFunctions()
    {
        return array(
            'update'  => new \Twig_Function_Method($this, 'update'),
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

    public function getName()
    {
        return 'brs_twig_extension';
    }

}