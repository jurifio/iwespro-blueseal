<?php
namespace bamboo\blueseal\controllers\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\core\intl\CLang;

/**
 * Class CProductListAjaxController
 * @package bamboo\blueseal\controllers\ajax
 *
 * @author Bambooshoot Team <emanuele@bambooshoot.agency>, ${DATE}
 *
 * @copyright (c) Bambooshoot snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @since ${VERSION}
 */
class CProductModelListAjaxController extends AAjaxController
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

        if ($this->app->getUser()->hasPermission('allShops')) {

        } else{
            $res = $this->app->dbAdapter->select('UserHasShop',['userId'=>$this->app->getUser()->getId()])->fetchAll();
            foreach($res as $val) {
                $this->authorizedShops[] = $val['shopId'];
            }
        }

        $this->em = new \stdClass();
        $this->em->products = $this->app->entityManagerFactory->create('ProductSheetModelPrototype');

        return $this->{$action}();
    }

    public function get()
    {
        $sql = "select `p`.`id` AS `id`,`p`.`name` AS `name`,`p`.`code` AS `code`,`p`.`productName` AS `productName`,`psp`.`name` AS `prototypeName`,group_concat(distinct `pct`.`name` separator ',') AS `categories`,group_concat(distinct `pdlt`.`name` separator ',') AS `labels`,group_concat(distinct `pdt`.`name` separator ',') AS `details` from (((((((((`ProductSheetModelPrototype` `p` join `ProductSheetPrototype` `psp` on((`p`.`productSheetPrototypeId` = `psp`.`id`))) left join `ProductSheetModelActual` `a` on((`p`.`id` = `a`.`productSheetModelPrototypeId`))) join `ProductDetailLabel` `pdl` on((`a`.`productDetailLabelId` = `pdl`.`id`))) left join `ProductDetailLabelTranslation` `pdlt` on((`pdlt`.`productDetailLabelId` = `pdl`.`id`))) join `ProductDetail` `pd` on((`pd`.`id` = `a`.`productDetailId`))) left join `ProductDetailTranslation` `pdt` on((`pdt`.`productDetailId` = `pd`.`id`))) left join `ProductSheetModelPrototypeHasProductCategory` `php` on((`php`.`productSheetModelPrototypeId` = `p`.`id`))) join `ProductCategory` `pc` on((`pc`.`id` = `php`.`productCategoryId`))) left join `ProductCategoryTranslation` `pct` on((`pct`.`productCategoryId` = `pc`.`id`))) where ((`pct`.`langId` = 1) and (`pdt`.`langId` = 1)) group by `p`.`id`";
        $datatable = new CDataTables($sql,['id'],$_GET,true);

	    //$datatable->addSearchColumn('extId');
	    //$datatable->addSearchColumn('extSkuId');

        $modelli = $this->app->repoFactory->create('ProductSheetModelPrototype')->em()->findBySql($datatable->getQuery(),$datatable->getParams());
        $count = $this->em->products->findCountBySql($datatable->getQuery(true), $datatable->getParams());
        $totalCount = $this->em->products->findCountBySql($datatable->getQuery('full'), $datatable->getParams());

        $modifica = $this->urls['base']."prodotti/modelli/modifica";

        $response = [];
        $response ['draw'] = $_GET['draw'];
        $response ['recordsTotal'] = $totalCount;
        $response ['recordsFiltered'] = $count;
        $response ['data'] = [];

        $i = 0;

        foreach($modelli as $val){

            $response['data'][$i]["DT_RowId"] = 'row__'.$val->id;
            $response['data'][$i]["DT_RowClass"] = 'colore';
            $response['data'][$i]['code'] = '<a data-toggle="tooltip" title="modifica" data-placement="right" href="'.$modifica.'?id='.$val->id.'">'.$val->code.'</a><br />';
            $response['data'][$i]['code'].= '<span class="small">(<a data-toggle="tooltip" title="Usa come modello" data-placement="right" href="'.$modifica.'?modelId='.$val->id.'">Usa come modello</a>)</span><br />';
            //$response['data'][$i]['sizeGroup'] = ($val->productSizeGroup) ? '<span class="small">' . $val->productSizeGroup->locale .  '-' . explode("-", $val->productSizeGroup->macroName)[0] . '</span>' : '';

            $response['data'][$i]['name'] = $val->name;
            $response['data'][$i]['productName'] = $val->productName;
            $response['data'][$i]['prototypeName'] = $val->productSheetPrototype->name;
            $cats = '<span class="small">';
            foreach ($val->productCategory as $cat) {
                $cats.= $cat->getLocalizedName() . "<br />";
            }
            $cats.= '</span>';
            $response['data'][$i]['categories'] = $cats;
            unset($cats);

            $response['data'][$i]['details'] = '<span class="small">';
            foreach ($val->productSheetModelActual as $det) {
                $response['data'][$i]['details'] .=
                    $det->productDetailLabel->slug .
                    ":" .
                    $det->productDetail->productDetailTranslation->getFirst()->name .
                    '<br />';
            }
            $response['data'][$i]['details'].= '</span>';

            $i++;
        }

        return json_encode($response);
    }
}