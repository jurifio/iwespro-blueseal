<?php

namespace bamboo\blueseal\business;

/**
 * Class CDataTables
 * @package bamboo\blueseal\business
 *
 * @author Bambooshoot Team <emanuele@bambooshoot.agency>
 *
 * @copyright (c) Bambooshoot snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 01/01/2014
 * @since 1.0
 */
class CDataTables
{
    /**
     * @var string
     */
    protected $table;
    /**
     * @var array
     */
    protected $keys;
    /**
     * @var array
     */
    protected $columns = [];
    /**
     * @var array
     */
    protected $conditions = [];
	/**
     * @var array
     */
    protected $likeConditions = [];
    /**
     * @var bool
     */
    protected $search = false;
    /**
     * @var array
     */
    protected $orders = [];
    /**
     * @var bool
     */
    protected $limit = false;
    /**
     * @var bool
     */
    protected $offset = false;
    /**
     * @var array
     */
    protected $params = [];
    /**
     * @var string
     */
    protected $rawSearch;
    /**
     * @var string
     */
    protected $where;

    /**
     * CDataTables constructor.
     * @param $table
     * @param array $keys
     * @param array $dtData
     */
    public function __construct($table, array $keys, array $dtData)
    {
        $this->setUpDtData($dtData);
        $this->keys = $keys;
        $this->table = $table;
    }

    /**
     * @param $dtData
     */
    public function setUpDtData($dtData)
    {
        $this->readColumns($dtData['columns']);
        $this->readOrder($dtData['order']);
        $this->readSearch($dtData);
        $this->readLimits($dtData);
    }

	/**
	 * @param $column
	 * @param array $values
	 * @param bool|false $not
	 */
    public function addCondition($column, array $values, $not = false){
        $this->conditions[] = [$column,$values,$not];
    }

	/**
	 * @param $column
	 * @param string $values
	 * @param bool|false $not
	 */
    public function addLikeCondition($column, $values, $not = false){
        $this->likeConditions[] = [$column,$values,$not];
    }

    /**
     * @param bool $raw
     * @return bool
     */
    public function getSearch($raw = true){
        return $raw ? $this->rawSearch : $this->search;
    }
    /**
     * @param bool|false $count
     * @return string
     */
    public function getQuery($count = false)
    {
        $sqlSelect = $this->select($count).$this->from();
        if($count){
            $sqlSelect.= $this->where($count);
        } else{
            $sqlSelect.= $this->where(false).$this->orderBy().$this->limit();
        }
        return $sqlSelect;
    }

    /**
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * @param bool|false $count
     * @return array
     */
    public function buildQuery($count = false)
    {
        return ["query"=>$this->getQuery($count), "params"=>$this->getParams()];
    }

    /**
     * @param bool $count
     * @return string
     */
    protected function select($count = false)
    {
        $keys = implode(',',$this->keys);
        return $count ? "SELECT COUNT( DISTINCT {$keys} ) " : "SELECT DISTINCT {$keys} ";

    }

    /**
     * @return string
     */
    protected function from()
    {
        return " FROM {$this->table} ";
    }

    /**
     *
     */
    public function reset(){
        $this->where = "";
        $this->params = [];
    }

    /**
     * @param bool $count
     * @return string
     */
    protected function where($count = false)
    {
        if(!empty($this->where)){
            return $this->where;
        }
        $conditions = [];
        $search = [];
        foreach ($this->conditions as $condition ){
            $single = $condition[0];
	        if($condition[2] == true){
		        $single.=" NOT ";
	        }
	        $single.=" IN (";
            for($i=0;$i<count($condition[1]);$i++){
                $single .= '?,';
                $this->params[] = $condition[1][$i];
            }
            $conditions[] = rtrim($single,', ').') ';
        }
	    foreach ($this->likeConditions as $condition ){
            $single = $condition[0];
	        if($condition[2] == true){
		        $single.=" NOT ";
	        }
	        $single.=" like ?";
		    $conditions[] = $single;
            $this->params[] = $condition[1];
        }

        if($count != 'full'){
            $columns = [];
            foreach ($this->columns as $idx => $column) {
                if ($column['searchable'] == true) {
                    if($this->search){
                        $columns[] = $column['name']." RLIKE ?";
                        $this->params[] = $this->search;
                    }
                    if(!empty($column['search'])){
                        $columns[] = $column['name']." RLIKE ?";
                        $this->params[] = $this->likeSearch($column['search']);
                    }
                }
            }
            if($this->search){
                foreach($this->keys as $key){
                    $search[] = $key." RLIKE ?";
                    $this->params[] = $this->search;
                }
            }
        }

        $conditions = empty($conditions) ? " 1=1 " : implode(' AND ' , $conditions );
        $search = empty($search) ? " 1=1 " : ' ( '.implode(' OR ',$search).' ) ';
        $columns = empty($columns) ? " 1=1 " : ' ( ' . implode(' AND ', $columns) . ' ) ';
        $this->where = " WHERE ".$conditions." AND ".$search . " AND " . $columns ;
        return $this->where;
        
    }

    

    /**
     * @param $string
     * @return mixed|string
     */
    protected function likeSearch($string)
    {
        $string = str_replace('.','\.', $string);
        $string = str_replace('*','.*', $string);
        $string.='.*';
        return $string;

    }

    /**
     * @return string
     */
    protected function orderBy()
    {
        if(!empty($this->orders)){
            $ord = [];
            foreach($this->orders as $column){
                $ord[] = "`".$column['column']."` ".$column['dir'];
            }
            return "ORDER BY ".implode(',',$ord);
        } else return " ";
    }

    /**
     * @return string
     */
    protected function limit()
    {
        if($this->limit){
            return " LIMIT ".$this->offset.",".$this->limit;
        }else{
            return " OFFSET ".$this->offset;
        }
    }

    /**
     * @param $columns
     */
    protected function readColumns($columns)
    {
        foreach($columns as $column){
            $key = (isset($column['name']) && $column['name'] != '') ? $column['name'] : $column['data'];
            $this->createColumn($key,$column['orderable'],$column['searchable'],$column['search']['value'],null);
        }
    }

    /**
     * @param $orders
     */
    protected function readOrder($orders)
    {
        foreach ($orders as $order) {
            $this->orders[] = ['column'=>$this->columns[$order['column']]['name'],'dir'=>!empty($order['dir']) ? $order['dir'] : 'asc' ];
        }
    }

    /**
     * @param $dtData
     */
    protected function readSearch($dtData)
    {
        if(isset($dtData['search']) && isset($dtData['search']['value']) && !empty($dtData['search']['value'])){
            $this->rawSearch = $dtData['search']['value'];
            $this->search = $this->likeSearch($dtData['search']['value']);
        }
    }

    /**
     * @param $dtData
     */
    protected function readLimits($dtData)
    {
        $this->limit = isset($dtData['length']) ? $dtData['length'] : false;
        $this->offset= isset($dtData['start']) ? $dtData['start'] : 0;
    }

    /**
     * @param $column
     * @param bool $sortable
     * @param bool $searchable
     * @param null $search
     * @param null $permission
     */
    protected function createColumn($column, $sortable = true, $searchable = true, $search = null, $permission = null)
    {
        $this->columns[] = ["name"=>$column,"sortable"=>filter_var($sortable, FILTER_VALIDATE_BOOLEAN),"searchable"=>filter_var($searchable, FILTER_VALIDATE_BOOLEAN),"search"=>$search,"permission"=>$permission,];
    }

    /**
     * @param $column
     */
    public function addSearchColumn($column)
	{
		$this->columns[] = ["name"=>$column,"sortable"=>false,"searchable"=>true,"search"=>$this->search,"permission"=>null];
	}
}