<?php

namespace BRS\CoreBundle\Core;

use BRS\CoreBundle\Core\Utility as util;
use Symfony\Bundle\DoctrineBundle\Registry;


/**
 * EntityLister manages paging results from a doctrine repository
 */
class EntityLister
{
	
	protected $doctrine;
	
	protected $repository;
	
	protected $entity_name;
	
	protected $entity_aliases = array();
	
	protected $list_fields;
	
	protected $filter_fields;
	
	protected $filters;
	
	protected $em;
	
	protected $query;
	
	protected $order_by;
	
	protected $page = 1;
	
	protected $page_size;
	
	/**
     * Create a new EntityLister
     *
	 * @param Registry $doctrine
     */
	public function __construct(Registry $doctrine)
	{	
		$this->doctrine = $doctrine;
		
		$this->em = $this->doctrine->getEntityManager();
	}
	
	/**
     * Gets the Doctrine object for this lister
     *
     * @return array The list fields
     */
	public function getDoctrine()
	{	
		return $this->$doctrine;
	}
	
	/**
     * Sets the doctrine entity name of the primary object
     *
     * @param string  $entity_name		The name of the entity
     */
	public function setEntity($entity_name){
		
        $repository = $this->em->getRepository($entity_name);
		
		$this->entity_name = $entity_name;
		$this->repository = $repository;
	}
	
	/**
     * Get a doctrine repository for a given repository defaults to the primary entity
	 * 
     * @param string  $entity_name		The name of the entity
     * @return EntityRepository			Repository for the given entity defaults to the primary one
     */
	public function getRepository($entity_name = null){
		
		if($entity_name){
			
			$em = $this->getDoctrine()->getEntityManager();

       		return $em->getRepository($entity_name);
			
		}else{
		
			return $this->$repository;
		}
	}
	
	/**
     * Sets list fields for default list module
     *
     * @param array  $fields       An array of field definitions
     */
	public function setListFields($fields){
		
		$this->list_fields = $fields;
	}
	
	/**
     * Gets current list fields for default list module
     *
     * @return array The list fields
     */
	public function getListFields(){
		
		return $this->list_fields;
	}
	
	/**
     * Sets filter fields for default list module
     *
     * @param array  $fields       An array of field definitions
     */
	public function setFilterFields($fields){
		
		$this->filter_fields = $fields;
	}
	
	/**
     * Gets current filter fields for default list module
     *
     * @return array The list fields
     */
	public function getFilterFields(){
		
		return $this->filter_fields;
	}
	
	/**
     * Sets filters for default list module
     *
     * @param array  $fields       An array of field definitions
     */
	public function setFilters($filters){
		
		$this->filters = $filters;
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
     * Sets list order
     *
     * @param array  $order_by       An array of field definitions
     */
	public function setOrderBy($order_by){
		
		$this->order_by = $order_by;
	}
	
	/**
     * Get list order
     *
     * @return the order by field
     */
	public function getOrderBy(){
		
		return $this->order_by;
	}
	
	/**
     * Sets page number
     *
     * @param array  $page       page number
     */
	public function setPage($page){
		
		if($page){
			$this->page = $page;
		}
	}
	
	/**
     * Get current page number
     *
     * @return the order by field
     */
	public function getPage(){
		
		return $this->page;
	}
	
	/**
     * Sets page size
     *
     * @param array  $page       page number
     */
	public function setPageSize($page_size){
		
		$this->page_size = $page_size;
	}
	
	/**
     * Get current page size
     *
     * @return the order by field
     */
	public function getPageSize(){
		
		return $this->page_size;
	}
	
	/**
     * Gets an array of entity data based on the seleced list fields and current filters
     *
     * @return array 	The entity data
     */
	public function getCount(){
		
		$query = $this->buildCountQuery();
		
		$array = $query->getArrayResult();
		
		//die(print_r($array, true));
		
		if($array){
			
			$count = $array[0][1];
		}
		
		return $count;
	}
	
	/**
     * Gets an array of entity data based on the seleced list fields and current filters
     *
     * @return array 	The entity data
     */
	public function getList(){
		
		$query = $this->buildListQuery();
		
		$array = $query->getArrayResult();
		
		return $array;
	}
	
	
	/**
     * deletes entities based on the current filters
     *
     * @return array 	The entity data
     */
	public function delete(){
		
		$query = $this->buildDeleteQuery();
		
		$result = $query->getResult();
		
		return $result;
	}
	
	/**
     * Gets the shorted alias for a given entity for use in query bulding
     *
	 * @param string	Full entity name
     * @return string	Shortened entity alias
     */
	private function getEntityAlias($entity_name = null){
		
		if(!$entity_name){
			
			$entity_name = $this->entity_name;
		}
		
		if(!isset($this->entity_aliases[$entity_name])){
		
			$class_name = substr(strrchr($entity_name, ":"), 1);	
			
			$alias = strtolower($class_name[0]);
			
			$this->entity_aliases[$entity_name] = $alias;
		
		}
			
		return $this->entity_aliases[$entity_name];
	}
	
	/**
     * Builds the field sting for use in the doctrine select query
     *
     * @return string	The select fields
     */
	private function buildFieldDQL(){
		
		$fields = $this->getListFields();
		
		$alias = $this->getEntityAlias();
		
		$pieces = array();
		
		$pieces[] = $alias . '.id';
		
		foreach((array)$fields as $key => $field){
			
			$pieces[] = $alias . '.' . $key;
		}
		
		$dql = implode(', ', $pieces);
		
		return $dql;
	}
	
	/**
     * Adds the list filters to an existing query builder
     *
     * @return QueryBuilder	The doctrine query builder object
     */
	private function addFieldFilters($qb){
		
		$fields = $this->getFilterFields();
		
		$alias = $this->getEntityAlias();
		
		//util::die_pre($fields);
		
		foreach((array)$fields as $key => $field){
			
			if(isset($field['value'])){
			
				$value = $field['value'];
				
				$field_ref = $alias . '.' . $key;
				
				if(!empty($value) && isset($field['type'])){
					
					switch($field['type']){
						
						case 'text':
						default:
							
							$filter = $field_ref . " LIKE '%$value%'";
					}
					
					$qb->andWhere($filter);
				}
			}
		}
		
		return $qb;
	}
	
	/**
     * Adds the list filters to an existing query builder
     *
     * @return QueryBuilder	The doctrine query builder object
     */
	private function addFilters($qb){
		
		$filters = $this->getFilters();
		
		$alias = $this->getEntityAlias();
		
		//util::die_pre($fields);
		
		foreach((array)$filters as $key => $filter){
			
			$qb->andWhere($filter['filter']);
			
			if(isset($filter['params'])){
			
				$qb->setParameters($filter['params']);
			}
		}
		
		return $qb;
	}
	
	/**
     * Builds a doctrine query to pull the list results
     *
     * @return Query	The doctrine query object
     */
	private function buildCountQuery(){
		
		$alias = $this->getEntityAlias();
		
		$qb = $this->em->createQueryBuilder();
		
		$fields = $this->buildFieldDQL();
		
		$qb->add('select', 'COUNT(' . $alias . '.id)')
		   ->add('from', $this->entity_name . ' ' . $alias);
		
		$qb = $this->addFieldFilters($qb);
		$qb = $this->addFilters($qb);
		
		$this->query = $qb->getQuery();
		  
		return $this->query;
	}
	
	/**
     * Builds a doctrine query to pull the list results
     *
     * @return Query	The doctrine query object
     */
	private function buildListQuery(){
		
		$alias = $this->getEntityAlias();
		
		$qb = $this->em->createQueryBuilder();
		
		$fields = $this->buildFieldDQL();
		
		$qb->add('select', $fields)
		   ->add('from', $this->entity_name . ' ' . $alias);
		
		$qb = $this->addFieldFilters($qb);
		
		$page = $this->getPage();
		$page_size = $this->getPageSize();
		
		if($page && $page_size){
			
		   $qb->setFirstResult( ($page - 1) * $page_size )
		      ->setMaxResults( $page_size );
		}
		
		$order_by = $this->getOrderBy();
		
		if($order_by){
			
			$qb->add('orderBy', $alias . '.' . $order_by);
		}
		
		$this->query = $qb->getQuery();
		  
		return $this->query;
	}
	
	/**
     * Builds a doctrine query to delete items by filter
     *
     * @return Query	The doctrine query object
     */
	private function buildDeleteQuery(){
		
		$alias = $this->getEntityAlias();
		
		$qb = $this->em->createQueryBuilder();
		
		$qb->delete($this->entity_name . ' ' . $alias);
		
		$qb = $this->addFieldFilters($qb);
		$qb = $this->addFilters($qb);
		
		$this->query = $qb->getQuery();
		  
		return $this->query;
	}
	
}
	