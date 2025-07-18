<?php

namespace bamboo\blueseal\business;

use bamboo\core\traits\TMySQLTimestamp;

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
    use TMySQLTimestamp;
    /**
     * @var array
     */
    public $responseSet = [];
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
     * @var array
     */
    protected $ignobleConditions = [];
    /**
     * @var bool
     */
    protected $search = false;
    /**
     * @var array
     */
    protected $groups = [];
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
     * @var bool
     */
    protected $isSubQuery;

    /**
     * CDataTables constructor.
     * @param $table
     * @param array $keys
     * @param array $dtData
     * @param bool $isSubQuery
     */
    public function __construct($table, array $keys, array $dtData, $isSubQuery = true)
    {
        $this->setUpDtData($dtData);
        $this->keys = $keys;
        $this->isSubQuery = $isSubQuery;
        if ($isSubQuery) $table = "(" . $table . ") t";
        $this->table = $table;

        $this->responseSet['draw'] = $_GET['draw'];
        $this->responseSet['data'] = [];
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
     * @param $columns
     */
    protected function readColumns($columns)
    {
        foreach ($columns as $column) {
            $key = (isset($column['name']) && $column['name'] != '') ? $column['name'] : $column['data'];
            $this->createColumn($key, $column['orderable'], $column['searchable'], null, null, $column['search']['value']);
        }
    }

    /**
     * @param $column
     * @param bool $sortable
     * @param bool $searchable
     * @param null $search
     * @param null $permission
     * @param null $filter
     */
    protected function createColumn($column, $sortable = true, $searchable = true, $search = null, $permission = null, $filter = null)
    {
        $this->columns[] = ["name" => $column, "sortable" => filter_var($sortable, FILTER_VALIDATE_BOOLEAN), "searchable" => filter_var($searchable, FILTER_VALIDATE_BOOLEAN), "search" => $search, "permission" => $permission, "filter" => $filter];
    }

    /**
     * @param $orders
     */
    protected function readOrder($orders)
    {
        foreach ($orders as $order) {
            $this->orders[] = ['column' => $this->columns[$order['column']]['name'], 'dir' => !empty($order['dir']) ? $order['dir'] : 'asc'];
        }
    }

    /**
     * @param $dtData
     */
    protected function readSearch($dtData)
    {
        if (isset($dtData['search']) && isset($dtData['search']['value']) && !empty($dtData['search']['value'])) {
            $this->rawSearch = $dtData['search']['value'];
            $this->search = $this->likeSearch($dtData['search']['value']);
//            if(value inizia per '-') aallora addcondition (not value)
        }
    }

    /**
     * @param $string
     * @param bool $startWith
     * @return string
     */
    protected function likeSearch($string, $startWith = true)
    {
        if (!$startWith) {
            $string = ".*" . $string;
        }
        //$string = str_replace('.','\.', $string);
        //$string = str_replace('*','.*', $string);
        return $string;//$string.".*";
    }

    /**
     * @param $dtData
     */
    protected function readLimits($dtData)
    {
        $this->limit = isset($dtData['length']) ? $dtData['length'] : false;
        $this->offset = isset($dtData['start']) ? $dtData['start'] : 0;
    }

    /**
     * @param $column
     * @param array $values
     * @param bool|false $not
     */
    public function addCondition($column, array $values, $not = false)
    {
        $this->conditions[] = [$column, $values, $not];
    }

    /**
     * @param $column
     * @param string $values
     * @param bool|false $not
     */
    public function addLikeCondition($column, $values, $not = false)
    {
        $this->likeConditions[] = [$column, $values, $not];
    }

    public function addIgnobleCondition($column, $values, $not = false)
    {
        $this->ignobleConditions[] = [$column, $values, $not];
    }

    /**
     * @param bool $raw
     * @return bool
     */
    public function getSearch($raw = true)
    {
        return $raw ? $this->rawSearch : $this->search;
    }

    /**
     * Does the query and save the result
     * @param bool $selectStar
     */
    public function doAllTheThings($selectStar = false)
    {
        $this->responseSet['selectSql'] = $this->getQuery(false,$selectStar);
        $this->responseSet['selectParams'] = $this->getParams();
        $microtime = microtime(true);
        $this->responseSet ['data'] =
            \Monkey::app()->dbAdapter->query(
                $this->responseSet['selectSql'],
                $this->responseSet['selectParams'], true)->fetchAll();
        $this->responseSet['selectTime'] = microtime(true) - $microtime;


        $this->responseSet['countSql'] = $this->getQuery(true);
        $this->responseSet['countParams'] = $this->getParams();
        $microtime = microtime(true);
        $this->responseSet ['recordsFiltered'] =
            \Monkey::app()->dbAdapter->query(
                $this->responseSet['countSql'],
                $this->responseSet['countParams'],true)->fetch()['conto'] ?? 0;
        $this->responseSet['countTime'] = microtime(true) - $microtime;

        $this->responseSet['fullCountSql'] = $this->getQuery('full');
        $this->responseSet['fullCountParams'] = $this->getParams();
        $microtime = microtime(true);
        $this->responseSet ['recordsTotal'] =
            \Monkey::app()->dbAdapter->query(
                $this->responseSet['fullCountSql'],
                $this->responseSet['fullCountParams'],true)->fetch()['conto'] ?? 0;
        $this->responseSet['fullCountTime'] = microtime(true) - $microtime;
    }

    /**
     * @param bool $count
     * @param bool $star
     * @return string
     */
    public function getQuery($count = false, $star = false)
    {
        $sqlSelect = $this->select($count, $star) . $this->from();
        if ($count === 'full') {
            $sqlSelect .= $this->where($count);
        } elseif ($count) {
            $sqlSelect .= $this->where($count);
        } else {
            $sqlSelect .= $this->where(false) . $this->groupBy() . $this->orderBy() . $this->limit();

        }
        return $sqlSelect;
    }

    /**
     * @param bool $count
     * @param bool $star
     * @return string
     */
    protected function select($count = false, $star = false)
    {
        if ($star) {
            $keys = '*';
            $this->addGroup($this->keys);
        } else {
            $keys = "DISTINCT " . implode(',', $this->keys);
        }

        return $count ? "SELECT COUNT( {$keys} ) as conto " : "SELECT {$keys} ";

    }

    /**
     * @param array $fields
     * @throws \Throwable
     */
    public function addGroup($fields = [])
    {
        if (!is_array($fields)) throw new \Exception('Il parametro Fields deve essere un array');
        foreach ($fields as $v) {
            $this->group[] = $v;
        }
    }

    /**
     * @return string
     */
    protected function from()
    {
        return " FROM {$this->table} ";
    }

    /**
     * @param bool $count
     * @return string
     */
    protected function where($count = false)
    {
        $this->where = "";
        $this->params = [];
        $conditions = [];
        $search = [];
        foreach ($this->conditions as $condition) {
            $conditions[] = $this->buildCondition($condition[0], $condition[1], $condition[2]);
        }
        foreach ($this->likeConditions as $condition) {
            $conditions[] = $this->buildCondition($condition[0], $condition[1], $condition[2]);
        }
        foreach ($this->ignobleConditions as $condition) {
            $not = '';
            if ($condition[2]) $not = 'NOT';
            $ingnobleCond = ' AND `' . $condition[0] . "` " . $not . " LIKE '" . $condition[1] . "'";
        }
        $columnsFilter = [];
        if ($count !== 'full') {
            foreach ($this->columns as $idx => $column) {
                if ($column['searchable'] == true) {
                    if ($this->search) {
                        $search[] = $this->buildCondition($column['name'], $this->search, false, false);
                    }
                    if ($column['search']) {
                        $search[] = $this->buildCondition($column['name'], $this->search, false, false); //"`" . $column['name']."` RLIKE ?";
                    }
                    if (array_key_exists('filter', $column) && ($column['filter'] || ("0" === $column['filter']))) {
                        $columnsFilter[] = $this->buildCondition($column['name'], $column['filter'], false, false);
                    }
                }
            }
            if ($this->search) {
                foreach ($this->keys as $key) {
                    $search[] = $this->buildCondition($key, $this->search, false);
                }
            }
        }

        $conditionsWhere = "( 1=1 ";
        foreach ($conditions as $condition) {
            $conditionsWhere .= " AND " . $condition['where'];
            array_push($this->params, ...$condition['params']);
        }
        $conditionsWhere .= " ) ";

        $columnsFilterWhere = " ( 1=1 ";
        foreach ($columnsFilter as $columnFilterElem) {
            $columnsFilterWhere .= " AND " . $columnFilterElem['where'];
            array_push($this->params, ...$columnFilterElem['params']);
        }
        $columnsFilterWhere .= " ) ";

        if (empty($search)) {
            $searchWhere = " 1=1 ";
        } else {
            $searchWhere = " ( 0=1 ";
            foreach ($search as $searchElem) {
                $searchWhere .= " OR " . $searchElem['where'];
                array_push($this->params, ...$searchElem['params']);
            }
            $searchWhere .= " ) ";
        }

        $this->where = " WHERE " . $conditionsWhere . " AND " . $columnsFilterWhere . ' AND ' . $searchWhere;
        if (isset($ingnobleCond)) $this->where .= $ingnobleCond;
        return $this->where;
    }

    /**
     * @param $field
     * @param $values
     * @param bool $not
     * @param bool $likeStartsWith
     * @return array
     */
    protected function buildCondition($field, $values, $not = false, $likeStartsWith = true)
    {
        $condition = " ";
        $condition .= "`" . $field . "` ";
        $params = [];
        //è un array indi per cui è per forza una in
        if (is_array($values)) {
            if ($not) $condition .= " NOT ";
            $condition .= " IN ( ";
            foreach ($values as $val) {
                $condition .= '?,';
                $params[] = $val;
            }
            $condition = rtrim($condition, ', ') . ') ';
        } //non è un array quindi sono altri cazzi, di sicuro una like
        elseif ($not && $values !== null) {
            $condition .= " NOT RLIKE ? ";
            $params[] = $this->likeSearch($values, $likeStartsWith);
        } elseif (strpos($values, '-') === 0) {
            $condition .= " NOT RLIKE ? ";
            $params[] = $this->likeSearch(substr($values, 1), $likeStartsWith);
        } elseif (strpos($values, '><') === 0) {
            $condition .= " BETWEEN ? AND ? ";
            $values = substr($values, 2);
            $values = explode("|", $values);
            $params[] = $values[0];
            if (isset($values[1])) {
                $params[] = $values[1];
            } else {
                $params[] = $this->time();
            }
        } elseif (strpos($values, '>') === 0) {
            $condition .= " > ?";
            if ($values instanceof \DateTime) {
                $values = $this->time($values->getTimestamp());
            }
            $params[] = substr($values, 1);
        } elseif (strpos($values, '<') === 0) {
            $condition .= " < ?";
            if ($values instanceof \DateTime) {
                $values = $this->time($values->getTimestamp());
            }
            $params[] = substr($values, 1);
        } elseif (strpos($values, '§in:') === 0) {
            $values = substr($values, 5);
            return $this->buildCondition($field, explode(',', $values));
        } elseif ($values === null) {
            $condition .= ' IS ' . (($not) ? 'NOT ' : '') . '?';
            $params[] = $values;
        } else {
            $condition .= " RLIKE ? ";
            $params[] = $this->likeSearch($values, $likeStartsWith);
        }

        return ["where" => $condition, "params" => $params];
    }

    /**
     * @return string
     */
    protected function groupBy()
    {
        if (!empty($this->group)) {
            $grp = [];
            foreach ($this->group as $column) {
                $grp[] = "`" . $column . "`";
            }
            return " GROUP BY " . implode(',', $grp) . " ";
        } else return " ";
    }

    /**
     * @return string
     */
    protected function orderBy()
    {
        if (!empty($this->orders)) {
            $ord = [];
            foreach ($this->orders as $column) {
                if ($this->isSubQuery) {
                    $ord[] = "t.`" . $column['column'] . "` " . $column['dir'];
                } else {
                    $ord[] = "`" . $column['column'] . "` " . $column['dir'];
                }
            }
            return "ORDER BY " . implode(',', $ord);
        } else return " ";
    }

    /**
     * @return string
     */
    protected function limit()
    {
        if ($this->limit === "0") {
            return " ";
        } elseif ($this->limit) {
            return " LIMIT " . $this->offset . "," . $this->limit;
        } else {
            return " OFFSET " . $this->offset;
        }
    }

    /**
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }

    public function getResponseSetData()
    {
        $this->responseSet['initTime'] = microtime(true);
        return $this->responseSet['data'];
    }

    public function setResponseDataSetRow($rowNum, $value)
    {
        $this->responseSet['data'][$rowNum] = $value;
    }

    /**
     * @param bool|false $count
     * @return array
     */
    public function buildQuery($count = false)
    {
        return ["query" => $this->getQuery($count), "params" => $this->getParams()];
    }

    /**
     *
     */
    public function reset()
    {

        $this->where = "";
        $this->params = [];
    }

    /**
     * @param $column
     */
    public function addSearchColumn($column)
    {
        $this->columns[] = ["name" => $column, "sortable" => false, "searchable" => true, "search" => $this->search, "permission" => null];
    }

    public function responseOut()
    {
        if(!isset($this->responseSet['resTime']) && isset($this->responseSet['initTime'])) {
            $this->responseSet['resTime'] = microtime(true) - $this->responseSet['initTime'];
            unset( $this->responseSet['initTime']);
        }
        \Monkey::app()->router->response()->setContentType("application/json");
        return json_encode($this->responseSet);
    }
}