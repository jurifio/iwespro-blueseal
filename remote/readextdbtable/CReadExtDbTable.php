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
 * @date 23/07/2018
 * @since 1.0
 */
class CReadExtDbTable extends AReadExtDbTable
{


    /**
     * @param $tableName
     * @param array $fields
     * @return array
     * @throws \bamboo\core\exceptions\BambooDBALException
     */
    public function readTables($tableName, array $fields = [])
    {
        $select = empty($fields) ? "*" : implode(',', $fields);
        $sql = "
        SELECT  $select
        FROM `$this->dbName`.`$tableName`
         ";

        $data = $this->client->query($sql, [])->fetchAll();

        return $data;
    }

    /**
     * @param bool $isEqual
     * @param string $remoteTable
     * @param array $remoteFields
     * @param array $remoteFieldsToSearch
     * @param string $localTable
     * @param array $localFields
     * @param array $localFieldsToSearch
     * @return int
     * @throws BambooException
     * @throws \bamboo\core\exceptions\BambooDBALException
     */
    public function insertData($isEqual = false,
                               string $remoteTable,
                               array $remoteFields,
                               array $remoteFieldsToSearch,
                               string $localTable,
                               array $localFields,
                               array $localFieldsToSearch)
    {

        //Prendo i dati dalla tabella remota
        $data = $this->readTables($remoteTable);

        //Vedo tutti i campi nella tabella locale
        $this->getTableFields($localTable);

        /** @var CRepo $table */
        $table = \Monkey::app()->repoFactory->create($localTable);

        $countRemoteKeys = count($remoteFieldsToSearch);
        $countLocalKeys = count($localFieldsToSearch);

        if ($countRemoteKeys != $countLocalKeys) throw new BambooException("The number of Local search keys in remote table must be identical to the number of Remote search keys");

        $extRow = 0;
        foreach ($data as $v) {

            $keys = [];

            for ($i = 0; $i < $countRemoteKeys; $i++) {
                $keys[$localFieldsToSearch[$i]] = $v[$remoteFieldsToSearch[$i]];
            }

            if(is_null($table->findOneBy($keys))) {

                $countLocalFields = count($localFields);
                $countRemoteFields = count($remoteFields);

                $fields = [];
                if($countLocalFields != $countRemoteFields) throw new BambooException("The number of Local fields in remote table must be identical to the number of Remote fields");

                for ($y = 0; $y < $countLocalFields; $y++) {
                    $fields[$localFields[$y]] = $v[$remoteFields[$y]];
                }

                //Se è nulla non l'ho trovata, quindi aggiorna
                //$newInstance = $table->getEmptyEntity();

            };

        }

    return $extRow;
    }


}