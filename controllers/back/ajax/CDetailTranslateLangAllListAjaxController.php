<?php
namespace bamboo\controllers\back\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\core\intl\CLang;

/**
 * Class CDetailTranslateLangAllListAjaxController
 * @package bamboo\blueseal\controllers\ajax
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
class CDetailTranslateLangAllListAjaxController extends AAjaxController
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
        $this->em->productsDetail = $this->app->entityManagerFactory->create('ProductDetail');

        return $this->{$action}();
    }

    public function get()
    {
        $langId = $this->app->router->request()->getRequestData('lang');
        $sql = "SELECT
  `view`.`id`                                     AS `id`,
  `view`.`slug`                                   AS `slug`,
  `view`.`translatedLangId`                       AS `translatedLangId`,
  `view`.`translatedName`                         AS `translatedName`,
  if((sum(`ProductSku`.`stockQty`) > 0), 1, 0)    AS `hasQuantity`,
  count(DISTINCT `ProductSku`.`productVariantId`) AS `timesDetailIsUsedInProduct`
FROM ((`vBluesealProductDetailTranslationStatus` `view`
  JOIN `ProductSheetActual` ON ((`ProductSheetActual`.`productDetailId` = `view`.`id`))) JOIN `ProductSku`
    ON (((`ProductSheetActual`.`productId` = `ProductSku`.`productId`) AND
         (`ProductSheetActual`.`productVariantId` = `ProductSku`.`productVariantId`))))
GROUP BY `view`.`id`";
        $datatable = new CDataTables($sql,['id'],$_GET, true);

        $okManage = $this->app->getUser()->hasPermission('/admin/product/edit');

        if (!empty($this->authorizedShops)) {
            $datatable->addCondition('shopId',$this->authorizedShops);
        }

        $productsDetail = \Monkey::app()->repoFactory->create('ProductDetail')->em()->findBySql($datatable->getQuery(),$datatable->getParams());
        $count = $this->em->productsDetail->findCountBySql($datatable->getQuery(true), $datatable->getParams());
        $totalCount = $this->em->productsDetail->findCountBySql($datatable->getQuery('full'), $datatable->getParams());

        $transRepo = \Monkey::app()->repoFactory->create('ProductDetailTranslation');

        $response = [];
        $response ['draw'] = $_GET['draw'];
        $response ['recordsTotal'] = $totalCount;
        $response ['recordsFiltered'] = $count;
        $response ['langId'] = $langId;
        $response ['data'] = [];

        $i = 0;

        foreach ($productsDetail as $val)
        {
            $trans = $transRepo->findOneBy(['productDetailId' => $val->id, 'langId' => $langId]);
            $transIta = $transRepo->findOneBy(['productDetailId' => $val->id, 'langId' => 1]);
            $name = '<div class="form-group form-group-default" style="width:604px">';
            if (!is_null($trans) && $okManage) {
                $name .= '<input type="text" class="form-control" style="width: 580px" id="name_' . $val->id . '" name="name_' . $val->id . '" value="' . $trans->name . '" onBlur="modifica(this,' . $langId . ')" />';
            } elseif ($okManage) {
                $name .= '<input type="text" class="form-control" style="width: 580px" id="name_' . $val->id . '" name="name_' . $val->id . '" onBlur="modifica(this,' . $langId . ')" />';
            }
            $name .= '</div>';

            $response['data'][$i]["DT_RowId"] = 'row__' . $val->id;
            $response['data'][$i]["DT_RowClass"] = 'colore';
            $response['data'][$i]['name'] = $name;
            $response['data'][$i]['slug'] = $transIta->name;
            $response['data'][$i]['id'] = $val->id;

            $i++;
        }

        return json_encode($response);
    }

    public function put()
    {
        $nameField = $this->app->router->request()->getRequestData('transId');
        $name = $this->app->router->request()->getRequestData('name');
        $names = explode('_', $nameField);
        $transId = $names[1];
        $langId = $this->app->router->request()->getRequestData('lang');

        \Monkey::app()->repoFactory->beginTransaction();
        try {
            $trans = \Monkey::app()->repoFactory->create('ProductDetailTranslation')->findOneBy(['productDetailId' => $transId, 'langId' => $langId]);
            if (!is_null($trans)) {
                if ($name == '') {
                    $trans->delete();
                } else {
                    $trans->name = $name;
                    $trans->update();
                }
            } elseif ($name != '') {
                $trans = \Monkey::app()->repoFactory->create("ProductDetailTranslation")->getEmptyEntity();

                $trans->productDetailId = $transId;
                $trans->langId = $langId;
                $trans->name = $name;
                $trans->insert();
            }
            \Monkey::app()->repoFactory->commit();
            return true;
        } catch (\Throwable $e) {
            \Monkey::app()->repoFactory->rollback();
        }
    }
}