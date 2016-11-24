<?php
/**
 * Created by PhpStorm.
 * User: enrico
 * Date: 09/11/16
 * Time: 18.32
 */

namespace bamboo\statistics;


use bamboo\domain\entities\CStatisticsHeader;

class CStatisticsManager
{
    /**
     * @var array
     * Ogni elemento dell'array ha come chiave il campo da usare come condizione e come valore un array avente
     * elementi con chiave contenente l'etichetta da visualizzare e come valore, il valore da usare nelle condizioni
     * della query
     */
    private $filters = [];

    /**
     * @var array in ingresso alle ricerche nelle entity
     */
    private $selectedFilters = [];
    /**
     * @var null
     */
    private $outputField = null;
    /**
     * @var string DAY|HOUR|WEEK|MONTH|YEAR
     */
    private $timeUnit = null;
    /**
     * @var array
     */
    private $timeRange = ['start' => null, 'stop' => null];
    /**
     * @var string 3|6
     */
    protected $dataInputType = 0;
    /**
     * @var array
     */
    private $groupDataType = ['SUM', 'MEDIUM', 'SUM-PREVIOUS'];

    /**
     * @var CStatisticsHeader
     */
    private $computedStatistics;

    protected function getComputedStatistics($tableName, $groupDataType, $dateGroup, $keyField) {
        $shR = \Monkey::app()->repoFactory->create('StatisticsHeader');
        $conditions = [
            'tableName' => $tableName,
            'groupDataType' => $groupDataType,
            'dateGroup' => $dateGroup,
            //'dateStart' => $dateStart,
            //'dateEnd' => $dateEnd,
            'keyField' => $keyField,
            'graphUnitName'
        ];
        //if ($graphUnitName) $conditions['graphUnitName'] = $graphUnitName;
        //if ($graphValueName) $conditions['graphValueName'] = $graphValueName;
        //if ($fullScaleValue) $conditions['fullScaleValue'] = $fullScaleValue;
        $sh = $shR->findOneBy($conditions);
        return $sh;
    }

    protected function createNewStatisticsData($tableName, $keyField, $selectedFilters, $groupDataType, $dateGroup, $fromDate = null, $toDate = null) {
        $fromDate = (false !== date_parse($fromDate)) ? $fromDate : $this->firstDate();
        //$toDate = (false !== date_parse($fromDate) or ())
        if ($this->isDbTable($tableName)) {
            //foreach($){}
            $repo = \Monkey::app()->repoFactory->create($tableName);
            $oc = $repo->findBy($selectedFilters);


            } else {

        }
    }

    protected function firstDate() {
        return \Monkey::app()->dbAdapter->query('SELECT min(time) FROM _Dates WHERE 1')->fetch();
    }

    /**
     * @param $tableName
     * @return bool
     */
    public function isDbTable($tableName) {
        $query = "SHOW TABLES LIKE '" . htmlentities($tableName) . "'";
        $res = \Monkey::app()->dbAdapter->query($query, [])->fetchAll();
        if (count($res)) return true;
        return false;
    }
}
?>