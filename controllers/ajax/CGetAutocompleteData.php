<?php
/**
 * Created by PhpStorm.
 * User: Fabrizio Marconi
 * Date: 20/05/2015
 * Time: 13:00
 */
namespace bamboo\blueseal\controllers\ajax;

/**
 * Class CGetAutocompleteData
 * @package redpanda\app\controllers
 */
class CGetAutocompleteData extends AAjaxController
{
    /**
     * @return bool
     */
    public function post()
    {
        $value = $_POST['value'];
        $keys = explode('_', $value);
        /** @var \redpanda\core\db\pandaorm\adapter\CMySQLAdapter $mysql */
        $mysql = $this->app->dbAdapter;
        $sql = 'SELECT distinct `name` FROM ProductAttributeValue where langId = ? and productAttributeId = ? ';
        $res = $mysql->query($sql,array($keys[1],$keys[2]))->fetchAll();
        $rres= array();
        foreach($res as $val){
            $rres[] = $val['name'];
        }
        $rres = implode(',', $rres);
        echo $rres;
    }

}