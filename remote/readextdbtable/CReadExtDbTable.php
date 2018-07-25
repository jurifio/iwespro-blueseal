<?php


namespace bamboo\blueseal\remote\readextdbtable;

use bamboo\core\db\pandaorm\repositories\CRepo;
use bamboo\core\exceptions\BambooException;


/**
 * Class CReadExtDbTable
 * @package bamboo\blueseal\remote\readextdbtable
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 24/07/2018
 * @since 1.0
 */
class CReadExtDbTable extends AReadExtDbTable
{
    /**
     * @param $tablesName
     * @param array $fields
     * @return array
     * @throws \bamboo\core\exceptions\BambooDBALException
     */
    public function readTables($tablesName, $remoteWhere, array $fields = [])
    {

        $firsTable = null;
        $firstField = null;
        $c = 0;
        $join = '';
        foreach ($tablesName as $table => $tFields) {
            $c++;
            if ($c == 1) continue;
            $join .= " JOIN ";

            $sumFields = count($tFields["Self"]);

            $tableForJoin = array_keys($tFields)[1];

            for ($countFieldJoin = 1; $countFieldJoin < $sumFields+1; $countFieldJoin++) {
                if($countFieldJoin === 1 && $sumFields !== 1) {
                    $join .= $this->dbName . '.' . $table . ' ON ' . $this->dbName . '.' . $table . '.' . $tFields['Self'][$countFieldJoin-1] . ' = ' . $this->dbName . '.' . $tableForJoin . '.' . $tFields[$tableForJoin][$countFieldJoin-1] . ' AND ';
                } else if ($countFieldJoin < $sumFields && $sumFields !== 1) {
                    $join .= $this->dbName . '.' . $table . '.' . $tFields['Self'][$countFieldJoin-1] . ' = ' . $this->dbName . '.' . $tableForJoin . '.' . $tFields[$tableForJoin][$countFieldJoin-1] . ' AND ';
                } else if($countFieldJoin === $sumFields && $sumFields !== 1) {
                    $join .= $this->dbName . '.' . $table . '.' . $tFields['Self'][$countFieldJoin-1] . ' = ' . $this->dbName . '.' . $tableForJoin . '.' . $tFields[$tableForJoin][$countFieldJoin-1];
                } else if($sumFields === 1){
                    $join .= $this->dbName . '.' . $table . ' ON ' . $this->dbName . '.' . $table . '.' . $tFields['Self'][$countFieldJoin-1] . ' = ' . $this->dbName . '.' . $tableForJoin . '.' . $tFields[$tableForJoin][$countFieldJoin-1];
                }
            }
        }


        $select = empty($fields) ? "*" : implode(',', $fields);
        $tableFrom = $tablesName[0];

        $sum = $this->countAssociativeArrayElements($remoteWhere);
        $count = 0;
        $countKeyTable = 0;
        $where = 'WHERE ';
        foreach ($remoteWhere as $tableN) {
            $tbName = array_keys($remoteWhere)[$countKeyTable];
            $countKeyTable++;
            foreach ($tableN as $condition => $val) {
                $count++;
                if ($count === $sum) {
                    $where .= $this->dbName . '.' . $tbName . '.' . $condition . ' = "' . $val . '"';
                } else {
                    $where .= $this->dbName . '.' . $tbName . '.' . $condition . ' = "' . $val . '" AND ';
                }
            }

        }


        $sql = "
        SELECT  $select
        FROM `$this->dbName`.`$tableFrom`
        $join
        $where 
         ";

        $data = $this->client->query($sql, [])->fetchAll();

        return $data;
    }

    /**
     * @param bool $isEqual
     * @param array $remoteTables
     * @param array $remoteFields
     * @param array $remoteFieldsToSearch
     * @param string $localTable
     * @param array $localFields
     * @param array $localFieldsToSearch
     * @param array $external
     * @return bool
     * @throws BambooException
     * @throws \bamboo\core\exceptions\BambooDBALException
     */
    // N.B. remoteTables and external must be an associative array with $key = tableName , $value = fieldKey --> esempio: User => id, UserDetails => userId
    public function insertData($isEqual = false,
                               array $remoteTables,
                               array $remoteFields,
                               array $remoteFieldsToSearch,
                               array $remoteWhere,
                               string $localTable,
                               array $localFields,
                               array $localFieldsToSearch,
                               array $external
    )
    {

        //Prendo i dati dalla tabella remota
        $data = $this->readTables($remoteTables, $remoteWhere);

        //todo: ----> implementare la funzione isEqual
        //$this->getTableFields($localTable);

        /** @var CRepo $table */
        $table = \Monkey::app()->repoFactory->create($localTable);


        $countRemoteKeys = count($remoteFieldsToSearch);
        $countLocalKeys = count($localFieldsToSearch);

        if ($countRemoteKeys != $countLocalKeys) throw new BambooException("The number of Local search keys in remote table must be identical to the number of Remote search keys");

        $countLocalFields = count($localFields);
        $countRemoteFields = count($remoteFields);

        if ($countLocalFields != $countRemoteFields) throw new BambooException("The number of Local fields in remote table must be identical to the number of Remote fields");

        try {
            \Monkey::app()->repoFactory->beginTransaction();
            foreach ($data as $v) {

                $keys = [];

                for ($i = 0; $i < $countRemoteKeys; $i++) {
                    $keys[$localFieldsToSearch[$i]] = $v[$remoteFieldsToSearch[$i]];
                }

                $exTable = $table->findOneBy($keys);

                if (is_null($exTable)) {

                    //Se è nulla non l'ho trovata, quindi aggiorna
                    $newInstance = $table->getEmptyEntity();
                    for ($y = 0; $y < $countLocalFields; $y++) {
                        $newInstance->{$localFields[$y]} = $v[$remoteFields[$y]];
                    }

                    if (!empty($external)) {
                        foreach ($external as $field => $value) {
                            $newInstance->{$field} = $value;
                        }
                    }
                    $newInstance->smartInsert();
                } else {
                    //todo implementare se esiste e è diverso allora -> aggiorna
                    for ($y = 0; $y < $countLocalFields; $y++) {
                        $exTable->{$localFields[$y]} = $v[$remoteFields[$y]];
                    }

                    if (!empty($external)) {
                        foreach ($external as $field => $value) {
                            $exTable->{$field} = $value;
                        }
                    }
                    $exTable->update();
                };

            }
            \Monkey::app()->repoFactory->commit();
        } catch (\Throwable $e) {
            \Monkey::app()->repoFactory->rollback();
            \Monkey::app()->applicationLog('ReadExtDbTable', 'Error', 'Rollback operation', $e);
        }

        return true;
    }

    private function countAssociativeArrayElements(array $arr)
    {

        $sum = 0;
        foreach ($arr as $val) {
            $sum = $sum + count($val);
        }

        return $sum;

    }


}