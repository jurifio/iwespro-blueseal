<?php
/**
 * Created by PhpStorm.
 * User: Fabrizio Marconi
 * Date: 20/05/2015
 * Time: 13:00
 */
namespace bamboo\controllers\back\ajax;

/**
 * Class CGetAutocompleteData
 * @package bamboo\app\controllers
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
        /** @var \bamboo\core\db\pandaorm\adapter\CMySQLAdapter $mysql */
        $mysql = $this->app->dbAdapter;
        $sql = 'SELECT distinct `name` FROM ProductDetailTranslation where langId = ?';
        $res = $mysql->query($sql,array($keys[1],$keys[2]))->fetchAll();
        $rres= array();
        foreach($res as $val){
            $rres[] = $val['name'];
        }
        $rres = implode(',', $rres);
        return $rres;
    }

    public function get()
    {
        $value = $_POST['value'];
        $keys = explode('_', $value);
        /** @var \bamboo\core\db\pandaorm\adapter\CMySQLAdapter $mysql */
        $mysql = $this->app->dbAdapter;
        $sql = 'SELECT distinct `id` FROM ProductDetailTranslation where langId = ? LIMIT 1';
        $res = $mysql->query($sql,array($keys[1],$keys[2]))->fetchAll();
        $rres= array();
        foreach($res as $val){
            $rres[] = $val['name'];
        }
        $rres = implode(',', $rres);
        return $rres;
    }
}