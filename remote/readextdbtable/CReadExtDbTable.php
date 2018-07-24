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
     * @param bool $full
     * @return mixed
     * @throws \bamboo\core\exceptions\BambooDBALException
     */
    public function readTables($tableName, array $fields, $full = false)
    {
        $select = $full ? "*" : implode(',', $fields);
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
     * @param array $localFieldsToCheck
     * @throws BambooException
     * @throws \bamboo\core\exceptions\BambooDBALException
     */
    public function insertData($isEqual = false,
                               string $remoteTable,
                               array $remoteFields,
                               array $remoteFieldsToSearch,
                               string $localTable,
                               array $localFieldsToCheck)
    {

        //Prendo i dati dalla tabella remota
        $data = $this->readTables($remoteTable, $remoteFields);

        //Vedo tutti i campi nella tabella locale
        $this->getTableFields($localTable);

        /** @var CRepo $table */
        $table = \Monkey::app()->repoFactory->create($localTable);

        $countRemoteKeys = count($remoteFieldsToSearch);
        $countLocalKeys = count($localFieldsToCheck);

        if($countRemoteKeys != $countLocalKeys) throw new BambooException("The number of Local search keys in remote table must be identical to the number of Remote search keys");

        foreach ($data as $v){

            $keys = '';

            for ($i = 0; $i < $countRemoteKeys; $i++) {
                $keys .= '"'.$localFieldsToCheck[$i].'"'. ' => ' . '"'.$v[$remoteFieldsToSearch[$i]].'"'.','
                ;
            }

            $a = json_decode($keys);

            $extRow = $table->findOneBy([
                $keys
            ]);

            $z = 3;
        }


      }

}