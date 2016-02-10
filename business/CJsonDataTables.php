<?php

namespace bamboo\blueseal\business;

use bamboo\core\io\CJsonAdapter;

class CJsonDataTables extends CDataTables
{
    protected $table;
    protected $keys;
    protected $columns = [];
    protected $conditions = [];
    protected $search = false;
    protected $orders = [];
    protected $limit = false;
    protected $offset = false;
    protected $params = [];
    protected $rawSearch;
    protected $where;
    protected $jsonAdapter;

    /**
     * @param array $dtData
     * @param CJsonAdapter $jsonAdapter
     */
    public function __construct(array $dtData, CJsonAdapter $jsonAdapter)
    {
        parent::__construct(null, [], $dtData);
        $this->setUpDtData($dtData);
        $this->setJsonAdapter($jsonAdapter);
    }

    /**
     * @param CJsonAdapter $adapter
     */
    public function setJsonAdapter(CJsonAdapter $adapter)
    {
        $this->jsonAdapter = $adapter;
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
        $this->conditions[] = [$column,$values];
    }

    /**
     * @param bool|true $raw
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
     * @param bool|false $count
     * @return bool
     */
    protected function select($count = false)
    {
        return true;
    }

    /**
     * @return string
     */
    protected function from()
    {
        return true;
    }

    public function reset(){
        $this->where = "";
        $this->params = [];
    }

    protected function where($count = false)
    {
        if(!empty($this->where)){
            return $this->where;
        }
        $conditions = [];
        $search = [];
        foreach ($this->conditions as $condition ){
            $single = $condition[0]." in (";
            for($i=0;$i<count($condition[1]);$i++){
                $single .= '?,';
                $this->params[] = $condition[1][$i];
            }
            $conditions[] = rtrim($single,', ').') ';
        }

        if($count != 'full'){
            foreach ($this->columns as $idx => $column) {
                if ($column['searchable'] == true) {
                    if($this->search){
                        $search[] = $column['name']." LIKE ?";
                        $this->params[] = $this->search;
                    }
                    if(!empty($column['search'])){
                        $search[] = $column['name']." LIKE ?";
                        $this->params[] = $this->likeSearch($column['search']);
                    }
                }
            }
            if($this->search){
                foreach($this->keys as $key){
                    $search[] = $key." LIKE ?";
                    $this->params[] = $this->search;
                }
            }
        }

        $conditions = empty($conditions) ? " 1=1 " : implode(' AND ' , $conditions );
        $search = empty($search) ? " 1=1 " : ' ( '.implode(' OR ',$search).' ) ';
        $this->where = " WHERE ".$conditions." AND ".$search;
        return $this->where;
    }

    protected function likeSearch($string)
    {
        $string = str_replace('%','\%', $string);
        $string = str_replace('*','%', $string);
        $string.='%';
        return $string;

    }

    protected function orderBy()
    {
        if(!empty($this->orders)){
            $ord = [];
            foreach($this->orders as $column){
                $ord[] = $column['column']." ".$column['dir'];
            }
            return "ORDER BY ".implode(',',$ord);
        } else return " ";
    }

    protected function limit()
    {
        if($this->limit){
            return " LIMIT ".$this->offset.",".$this->limit;
        }else{
            return " OFFSET ".$this->offset;
        }
    }

    protected function readColumns($columns)
    {
        foreach($columns as $column){
            $this->createColumn($column['data'],$column['orderable'],$column['searchable'],$column['search']['value'],null);
        }
    }

    protected function readOrder($orders)
    {
        foreach ($orders as $order) {
            $this->orders[] = ['column'=>$this->columns[$order['column']]['name'],'dir'=>!empty($order['dir']) ? $order['dir'] : 'asc' ];
        }
    }

    protected function readSearch($dtData)
    {
        if(isset($dtData['search']) && isset($dtData['search']['value']) && !empty($dtData['search']['value'])){
            $this->rawSearch = $dtData['search']['value'];
            $this->search = $this->likeSearch($dtData['search']['value']);
        }
    }
    protected function readLimits($dtData)
    {
        $this->limit = isset($dtData['length']) ? $dtData['length'] : false;
        $this->offset= isset($dtData['start']) ? $dtData['start'] : 0;
    }

    protected function createColumn($column,  $sortable = true, $searchable = true, $search = null, $permission = null)
    {
        $this->columns[] = ["name"=>$column,"sortable"=>filter_var($sortable, FILTER_VALIDATE_BOOLEAN),"searchable"=>filter_var($searchable, FILTER_VALIDATE_BOOLEAN),"search"=>$search,"permission"=>$permission,];
    }
}