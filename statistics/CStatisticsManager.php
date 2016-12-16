<?php
/**
 * Created by PhpStorm.
 * User: enrico
 * Date: 09/11/16
 * Time: 18.32
 */

namespace bamboo\statistics;


use bamboo\core\exceptions\BambooException;
use bamboo\domain\entities\CStatisticsHeader;

class CStatisticsManager
{
    /**
     * @var string
     */
    private $filterOnLogValue;
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
    private $subEntity = null;
    /**
     * @var null
     */
    private $outputField = null;
    /**
     * @var array
     */
    private $timeUnits = ['DAY' => 'd', 'HOUR' => 'h', 'WEEK' => 'm', 'MONTH' => 'm', 'YEAR' => 'Y'];
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
    private $groupDataType = ['countRows', 'countDistinct', 'sumValues', 'mediumValues', 'sumPrevious', 'countPrevious'];

    /**
     * @var CStatisticsHeader
     */
    private $computedStatistics;

    /**
     * @var array last extracted logs from Log table;
     */
    private $logs = [];

    /**
     * @var
     */
    private $filteredLogs = [];

    /**
     * @var array
     */
    private $entityFilters;

    protected function getComputedStatistics($tableName, $groupDataType, $dateGroup, $keyField)
    {
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

    protected function createNewStatisticsData(
        $filters = [
            ['userId'] => null,
            ['entityName'] => null,
            ['stringId'] => null,
            ['eventName'] => null,
            ['eventValue'] => null,
            ['actionName'] => null,
            ['backtrace'] => null,
            ['entityFilters'] => [],
        ],
        $dateStart = null,
        $dateEnd = null)
    {
        $this->entityFilters = $filters['entityFilters'];
        $filters = array_filter($filters, function ($val) {
            return (null !== $val);
        });
        $dba = \Monkey::app()->dbAdapter;
    protected function createNewStatisticsData($tableName, $keyField, $selectedFilters, $groupDataType, $dateGroup, $fromDate = null, $toDate = null) {
        $fromDate = (false !== date_parse($fromDate)) ? $fromDate : $this->firstDate();
        //$toDate = (false !== date_parse($fromDate) or ())
        if ($this->isDbTable($tableName)) {
            //foreach($){}
            $repo = \Monkey::app()->repoFactory->create($tableName);
            $oc = $repo->findBy($selectedFilters);

        //$where = ['AND' => $filters];
        //$where = $dba->where($where);

        $whereArr = [];
        $bind = [];
        foreach ($filters as $k => $f) {
            $whereArr[] = $k . ' = :' . $k;
            $bind[] = $f;
        }

        if ($dateStart) {
            $dateStart = $this->dateControl($dateStart);
            $whereArr[] = '`time` >= :dateStart';
            $bind[] = $dateStart;
        }

        if ($dateEnd) {
            $dateEnd = $this->dateControl($dateEnd);
            $whereArr[] = '`time` >= :dateEnd';
            $bind[] = $dateEnd;
        }

        $where = implode(' AND ', $whereArr[]);

        $query = "SELECT * FROM Log WHERE " . $where . " ORDER BY `time`";
        $logs = $dba->query($query, $bind)->fetchAll();
        $this->logs = $logs;
        //if (count($filters['entityFilters'])) return $this->filterLogs();
        return $this;
    }

    /**
     * @param $groupDataType
     */
    private function filterLogs()
    {
        foreach ($this->logs as $v) {
            if ($this->compareEntityWithFilters($v['entityName'], $v['stringId'], $v['eventValue'])) {
                $this->filteredLogs = $v;
            }
        }
    }

    /**
     * @param $entityName
     * @param $stringId
     */
    private function compareEntityWithFilters($entityName, $stringId, $eventValue = null)
    {
        $ent = \Monkey::app()->repoFactory->create($entityName)->findOneByStringId($stringId);
        $return = true;
        foreach ($this->selectedFilters as $name => $val) {
            if ($name === $this->filterOnLogValue) {
                $gotElementValue = $eventValue;
            } else {
                $gotElementValue = $this->getElementValueToCompare($ent->{$name}, $val);
            }
            $filterValue = $this->getFilterValueToCompare($val);
        }
    }

    /**
     * @param $filter
     */
    private function getFilterValueToCompare($filter) {
        $arrValue = array_values($filter)[0];
        if (is_array($arrValue)) {
            return $this->getFilterValueToCompare($arrValue);
        } else {
            return $arrValue;
        }
    }

    /**
     * @param $obj
     * @param $filter
     * @return array
     * @throws BambooException
     */
    private function getElementValueToCompare($obj, $filter)
    {
        $arrValue = array_values($filter)[0];
        if (is_array($arrValue)) {
                if (1 !== count($filter)) {
                    throw new BambooException ('selectedFilters \' trees\' elements can\'t have multiple ' );
                }
                return $this->getElementValueToCompare($obj->{key($filter)}, $arrValue);
            }
            if (is_string($arrValue) || is_numeric($arrValue)) {
                return $arrValue;
            }
    }

    private function dateControl($date) {
        if (is_string($date)) {
            $ret = strtotime($date);
            if ( false === $ret) throw new BambooException('Can\'t read as date and time the given string');
            return $date;
        } elseif (is_numeric($date)) {
            $ret = date('Y-m-d H:i:s', $date);
            if (false === $ret) throw new BambooException('invalid timestamp');
            return $ret;
        } else {
           return false;
        }
    }

    private function explodeDates($time, $timeUnit = null) {
        $time = new DateTime($time);
        if (!$timeUnit) $timeUnit = 'Y-m-d H:i:s';
        return $time->format($timeUnit);
    }

    /**
     * @param array|string $entityName
     * @param bool|integer $directFilter
     */
    public function addFilters($repoName, $directFilter = false)
    {
        if (is_array($repoName)) {
            foreach ($repoName as $v) {
                $this->isRepo($repoName);
                $this->filters[] = $v;
            }
            if (is_numeric($directFilter)) {
                if (array_key_exists($directFilter, $repoName)) $this->filterOnLogValue = $repoName[$directFilter];
                else throw new BambooException('given directFilter key doesn\'t exists in the $repoName');
            }
        }
        if (is_string($repoName)) {
            if ($this->isRepo($repoName)) {
                $this->filters[] = $repoName;
                if (true == $directFilter) $this->filterOnLogValue = $repoName;
            }
        }
    }

    /**
     * @param $repoName
     * @param bool $throwable
     * @return \bamboo\core\db\pandaorm\repositories\ARepo|bool
     * @throws BambooException
     */
    private function isRepo($repoName, $throwable = true) {
        $repo = \Monkey::app()->repoFactory->create($repoName);
        if (false !== strpos(get_class($repo), 'CRepo')) return $repo;
        if ($throwable) throw new BambooException('the Entity doesn\'t exists');
        else return false;
    }
}
?>