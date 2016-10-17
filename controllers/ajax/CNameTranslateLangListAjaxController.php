<?php
namespace bamboo\blueseal\controllers\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\core\exceptions\BambooException;
use bamboo\core\intl\CLang;


/**
 * Class CNameTranslateLangListAjaxController
 * @package redpanda\blueseal\controllers\ajax
 *
 * @author Bambooshoot Team <emanuele@bambooshoot.agency>
 *
 * @copyright (c) Bambooshoot snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date $date
 * @since 1.0
 */
class CNameTranslateLangListAjaxController extends AAjaxController
{
    protected $urls = [];
    protected $authorizedShops = [];
    protected $em;

    /**
     * @param $action
     * @return mixed
     */
    public function createAction($action)
    {
        $this->app->setLang(new CLang(1,'it'));
        $this->urls['base'] = $this->app->baseUrl(false)."/blueseal/";
        $this->urls['page'] = $this->urls['base']."prodotti";
        $this->urls['dummy'] = $this->app->cfg()->fetch('paths','dummyUrl');

        $this->em = new \stdClass();
        $this->em->productsName = $this->app->entityManagerFactory->create('ProductNameTranslation');

        return $this->{$action}();
    }

    public function get()
    {
        $langId = $this->app->router->request()->getRequestData('lang');
        $datatable = new CDataTables('vBluesealProductNameList',['id'],$_GET);

        $okManage = $this->app->getUser()->hasPermission('/admin/product/edit');

        if (!empty($this->authorizedShops)) {
            $datatable->addCondition('shopId',$this->authorizedShops);
        }

        $datatable->addCondition('langId',[1]);
        $datatable->addCondition('name',[''],true);

        $pnRepo = \Monkey::app()->repoFactory->create('ProductName');

        $productsName = $pnRepo->em()->findBySql($datatable->getQuery(),$datatable->getParams());
        $count = $this->em->productsName->findCountBySql($datatable->getQuery(true), $datatable->getParams());
        $totalCount = $this->em->productsName->findCountBySql($datatable->getQuery('full'), $datatable->getParams());

        $response = [];
        $response ['draw'] = $_GET['draw'];
        $response ['recordsTotal'] = $totalCount;
        $response ['recordsFiltered'] = $count;
        $response ['data'] = [];

        $i = 0;

        foreach($productsName as $val){

            $pnTranslated = $pnRepo->findOneBy(['name' => $val->name, 'langId' => $langId]);
            $translated = ($pnTranslated) ? trim($pnTranslated->translation) : '';
            $name = '<div class="form-group form-group-default full-width">';
            if ($okManage) {
                $name .= '<input type="text" class="form-control full-width nameId" data-lang="' . $langId . '" data-action="' . $this->urls['base'] . 'xhr/NameTranslateLangListAjaxController" data-name="' . $val->name . '" title="nameId" class="nameId" value="' . htmlentities($translated) . '"/>';
            }
            $name .= '</div>';

            $response['data'][$i]["DT_RowId"] = 'row__' . $val->id;
            $response['data'][$i]["DT_RowClass"] = 'colore';
            $response['data'][$i]['trans'] = $name;
            $response['data'][$i]['name'] = $val->name;
            $i++;
        }
        return json_encode($response);
    }

    public function put()
    {
        $name = trim(\Monkey::app()->router->request()->getRequestData('name'));
        $translated = trim(\Monkey::app()->router->request()->getRequestData('translated'));
        if ("" == $translated) return false;
        $langId = \Monkey::app()->router->request()->getRequestData('lang');

        $pnRepo = \Monkey::app()->repoFactory->create('ProductName');
        $pntRepo = \Monkey::app()->repoFactory->create('ProductNameTranslation');

        $pn = $pnRepo->findOneBy(['name' => $name, 'langId' => 1]);
        if (!$pn) throw new BambooException('OOPS! Non si può inserire una traduzione se non esiste il nome in italiano');

        $this->app->dbAdapter->beginTransaction();
        try {
            $pntRepo->insertTranslation($name, $langId, $translated);
            $this->app->dbAdapter->commit();
            return true;
        }  catch (\Throwable $e) {
            $this->app->dbAdapter->rollBack();
           return $e->getMessage();
        }
    }
}