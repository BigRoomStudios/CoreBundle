<?php

namespace BRS\CoreBundle\Widget;

use BRS\CoreBundle\Core\Widget\ListWidget;

/**
 * Member list widget
 * 
 */
class MemberList extends ListWidget
{
		
	//protected $template = 'BRSCoreBundle:Widget:member_list.html.twig';
		
	public function __construct()
	{
		parent::__construct();	
		
		$this->setEntityName('BRSCoreBundle:Member');
		
		$list_fields = array(
	
			'edit' => array(
				'type' => 'link',
				'route' => array(
					'name' => 'brs_admin_member_edit',
					'params' => array('id'),
				),
				'label' => 'edit',
				'width' => 100,
				'nonentity' => true,
			),
			
			'first_name' => array(
				'type' => 'text',
			),
			
			'last_name' => array(
				'type' => 'text',
			),
			
			'email' => array(
				'type' => 'email',
				//'width' => 100,
			),
		);
		
		$this->setListFields($list_fields);
	}
}
	