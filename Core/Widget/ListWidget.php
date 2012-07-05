<?php

namespace BRS\CoreBundle\Core\Widget;

use BRS\CoreBundle\Core\EntityLister;
use BRS\CoreBundle\Core\Utility;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\EventDispatcher\Event;

/**
 * ListWidget defines a basic list widget handles sorting and paging
 */
class ListWidget extends FormWidget
{
	
	protected $list_fields;
	
	protected $search_fields;

	protected $search_widget;
	
	protected $lister;
	
	protected $template = 'BRSCoreBundle:Widget:list.html.twig';
	
	protected $order_by;
	
	protected $page_size = 10;
	
	protected $selectable = true;
	
	protected $joins;
	
	protected $filters;
	
	public function setListFields($fields)
	{
		$this->list_fields = $fields;
	}
	
	public function getListFields()
	{
		return $this->list_fields;
	}
	
	/**
     * Sets list order
     *
     * @param array  $order_by       An array of field definitions
     */
	public function setDefaultOrderBy($order_by){
		
		$order = $this->sessionGet('order');
		
		if(!$order){
			
			$this->setOrderBy($order_by);
		}	
	}
	
	/**
     * Sets list order
     *
     * @param array  $order_by       An array of field definitions
     */
	public function setOrderBy($order_by){
		
		$this->sessionSet('order', $order_by);
	}
	
	/**
     * Get list order
     *
     * @return the order by field
     */
	public function getOrderBy(){
		
		return $this->sessionGet('order');
	}
	
	
	public function setSearchWidget(&$search_widget){
		
		$this->search_widget =& $search_widget;
		
		$search_widget->addListener($this, 'search.post', 'onSearchEvent');
	}
	
	public function getSearchWidget(){
		
		return $this->search_widget;
	}
	
	public function setJoins($joins){
		
		$this->joins = $joins;
	}
	
	public function getJoins(){
		
		return $this->joins;
	}
	
	public function addJoin($link, $alias, $join_type = 'leftJoin'){
		
		$this->joins[$link] = array(
			'link' => $link,
			'alias' => $alias,
			'type' => $join_type,
		);
	}
	
	/**
     * Sets filters for default list module
     *
     * @param array  $fields       An array of field definitions
     */
	public function setFilters($filters){
		
		$this->filters = $filters;
		
		$this->sessionSet('filters', $this->filters);
	}
	
	/**
     * Gets current filters for default list module
     *
     * @return array The list fields
     */
	public function getFilters(){
		
		return $this->filters;
	}
	
	/**
     * Gets an EntityLister for the primary entity
	 * 
     * @return array The list fields
     */
	public function &getLister()
	{
		if(!$this->lister){
				
			$lister = new EntityLister($this->get('doctrine'));
			
			$lister->setEntity($this->entity_name);
			
			//Utility::die_pre($this->getJoins());
			
			$lister->setJoins($this->getJoins());
			
			$lister->setFilters($this->getFilters());
			
			$fields = $this->getListFields();
			
			$list_fields = array();
			
			foreach($fields as $key => $field){
				
				if(!isset($field['nonentity'])){
					
					$list_fields[$key] = $field;
				}
				
			}
			
			$lister->setListFields($list_fields);
			
			$this->lister = $lister;		
		}
		
		$search_widget = $this->getSearchWidget();
			
		if($search_widget){
			
			$filter_fields = $search_widget->getFilterFields();
			
			$this->lister->setFilterFields($filter_fields);
		}
			
		return $this->lister;
	}
	
	public function onSearchEvent($event){
		
		$this->sessionSet('page', 1);
				
		$this->sessionSet('select_all', false);
	
		$this->sessionSet('selected', null);
	}
	
	public function createSession(){
		
		//$this->sessionSet('joins', $this->joins);
		
		$this->sessionSet('filters', $this->filters);
	}
	
	public function restoreSession(){
		
		//$session = $this->getRequest()->getSession();
		
		//$this->joins = $this->sessionGet('joins');
		
		//$this->filters = $this->sessionGet('filters');
		
		$lister =& $this->getLister();
		
		//$joins = $this->sessionGet('joins');
		
		$filters = $this->sessionGet('filters');
		
		if($filters){
			
			$lister->setFilters($filters);
		}
		
		$order = $this->sessionGet('order');
		
		$page = $this->sessionGet('page');
		
		$page_size = $this->sessionGet('page_size');
		
		if($order){
			
			$lister->setOrderBy($order);
		}
		
		if($page){
			
			$lister->setPage($page);
			
		}else{
			
			$this->sessionSet('page', 1);
		}
		
		if($page_size){
			
			$lister->setPageSize($page_size);
			
		}else{
			
			$this->sessionSet('page_size', $this->page_size);
			
			$lister->setPageSize($this->page_size);
		}
	}
	
	public function getList()
	{
		$lister =& $this->getLister();
		
		$list = $lister->getList();
		
		return $list;
	}
	
	public function getVars($render = true){
		
		$this->handleRequest();
		
		$this->restoreSession();
		
		$list = array();
		
		$count = 0;
		
		$pages = 1;
					
		$lister =& $this->getLister();
		
		$count = $lister->getCount();
		
		$list = $this->getList();
		
		$page = $this->sessionGet('page');
		
		$page_size = $this->sessionGet('page_size');
					
		$select_all = $this->sessionGet('select_all');
		
		$selected = $this->sessionGet('selected');
		
		$pages = ceil($count / $page_size);
		
		
		$add_vars = array(
			'list_fields' => $this->getListFields(),
			'list' => $list,
			'order_by' => $this->getOrderBy(),
			'rows' => count($list),
			'total' => $count,
			'page' => $page,
			'pages' => $pages,
			'page_size' => $page_size,
			'selectable' => $this->selectable,
			'select_all' => $select_all,
			'selected' => $selected,
		);
		
		$vars = array_merge(parent::getVars(), $add_vars);
		
		return $vars;
	}
	
	public function handleRequest()
	{	
		$request = $this->getRequest();
		
		if($request->getMethod() == 'GET'){
			
			$get = $request->query->get($this->getName());
			
			if(isset($get['order'])){
				
				$this->sessionSet('order', $get['order']);
			}
			
			if(isset($get['page'])){
			
				$this->sessionSet('page', $get['page']);
			}
			
			if(isset($get['page_size'])){
			
				$this->sessionSet('page_size', $get['page_size']);
			}
	    }
		
		
	}
	
	/**
	 * get json list data
	 *
	 * @Route("/data")
	 */
	public function dataAction()
	{	
		$vars = $this->getVars(false);
		
		return $this->jsonResponse($vars);
	}
	
	/**
	 * get a set of rendered rows
	 *
	 * @Route("/rows")
	 */
	public function rowsAction()
	{	
		$view = 'BRSCoreBundle:Widget:list/rows.html.twig';
			
		$vars = $this->getVars(false);
		
		if($this->isAjax()){
			
			$vars['rendered'] = $this->container->get('templating')->render($view, $vars);
			
			unset($vars['list']);
			
			return $this->jsonResponse($vars);
				
		}else{
			
			$response = new Response();
			
			return $this->container->get('templating')->renderResponse($view, $vars, $response);
		}
	}
	
	/**
	 * select all items
	 *
	 * @Route("/select_all")
	 */
	public function selectAllAction()
	{	
		$request = $this->getRequest();
		
		if($request->getMethod() == 'POST'){
			
			$this->sessionSet('select_all', true);
			
			$this->sessionSet('selected', array());
			
			if($this->isAjax()){
				
				return $this->jsonResponse(array('success' => true));
			}
		}
	}
	
	/**
	 * select all items
	 *
	 * @Route("/select_none")
	 */
	public function selectNoneAction()
	{	
		$request = $this->getRequest();
		
		if($request->getMethod() == 'POST'){
			
			$this->sessionSet('select_all', false);
			
			$this->sessionSet('selected', array());
			
			if($this->isAjax()){
				
				return $this->jsonResponse(array('success' => true));
			}
		}
	}
	
	/**
	 * delete selected items
	 *
	 * @Route("/delete_selected")
	 */
	public function deleteSelectedAction()
	{	
		$request = $this->getRequest();
		
		if($request->getMethod() == 'POST'){
			
			$selected = $request->get('selected');
			
			$this->sessionSet('selected', $selected);
			
			$select_all = $this->sessionGet('select_all');
			
			//$this->restoreSession();
			
			if($selected){
				
				$lister = $this->getLister();
				
				$alias = $lister->getEntityAlias();
				
				$filters = array();
				
				$id_filters = array();
				
				$params = array();
				
				$count = 0;
			
				foreach($selected as $id => $selected){
					
					if($select_all){
						
						if(!$selected){
							
							$count++;
							
							$id_filters[] = $alias . '.id != :id' . $count;
							
							$params['id'.$count] = $id;
						}
						
					}else{
					
						if($selected){
							
							$count++;
							
							$id_filters[] = $alias . '.id = :id' . $count;
							
							$params['id'.$count] = $id;
						}
					}
				}
				
				if($select_all){
					
					$id_filter = implode(' AND ', $id_filters);
					
				}else{
				
					$id_filter = implode(' OR ', $id_filters);
				}
				
				//die(print_r($selected));
				
				$current_filters = (array)$this->sessionGet('filters');
				
				//$current_filters = (array)$this->getFilters();
				
				//Utility::die_pre($current_filters);
				
				if($id_filter){
				
					$filters[] = array('filter' => "( $id_filter )", 'params' => $params);
				}
				
				//die(print_r($filters));
				
				$lister->setFilters(array_merge($current_filters, $filters));
				
				$lister->delete();
				
				$lister->setFilters($current_filters);
				
				$this->sessionSet('page', 1);
				
				$this->sessionSet('select_all', false);
				
				$this->sessionSet('selected', null);
			}
			
			return $this->rowsAction();
		}
	}
	
}
	