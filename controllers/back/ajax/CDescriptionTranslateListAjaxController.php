<?php
namespace bamboo\controllers\back\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\core\intl\CLang;


/**
 * Class CDescriptionTranslateListAjaxController
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
class CDescriptionTranslateListAjaxController extends AAjaxController
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
        $this->em->productsDesc = $this->app->entityManagerFactory->create('ProductDescriptionTranslation');

        return $this->{$action}();
    }

    public function get()
    {
        $sql = "SELECT
  `pd`.`productId`        AS `productId`,
  `pd`.`productVariantId` AS `productVariantId`,
  1                       AS `marketplaceId`,
  `pd`.`langId`           AS `langId`,
  `l`.`lang`              AS `lang`,
  `l`.`name`              AS `langName`,
  `pd`.`description`      AS `description`
FROM (`ProductDescriptionTranslation` `pd`
  JOIN `Lang` `l`)
WHERE ((`pd`.`langId` = `l`.`id`) AND (`pd`.`description` <> '') AND (`pd`.`description` <> '<br>') AND
       (`pd`.`description` <> '<br><br>'))";
        $datatable = new CDataTables($sql,['productId','productVariantId','marketplaceId','langId'],$_GET,true);
        $modifica = $this->urls['base']."traduzioni/descrizioni/modifica";

        $okManage = $this->app->getUser()->hasPermission('/admin/product/edit');

        if (!empty($this->authorizedShops)) {
            $datatable->addCondition('shopId',$this->authorizedShops);
        }
        $datatable->addCondition('langId',[1]);

        $productsDesc = \Monkey::app()->repoFactory->create('ProductDescriptionTranslation')->em()->findBySql($datatable->getQuery(),$datatable->getParams());
        $count = $this->em->productsDesc->findCountBySql($datatable->getQuery(true), $datatable->getParams());
        $totalCount = $this->em->productsDesc->findCountBySql($datatable->getQuery('full'), $datatable->getParams());

        $transRepo = \Monkey::app()->repoFactory->create('ProductDescriptionTranslation');
        $repo = \Monkey::app()->repoFactory->create('Lang');
        $installedLang = $repo->findAll();

        $response = [];
        $response ['draw'] = $_GET['draw'];
        $response ['recordsTotal'] = $totalCount;
        $response ['recordsFiltered'] = $count;
        $response ['data'] = [];

        $i = 0;

        foreach($productsDesc as $val){
            $desc = substr(strip_tags($val->description),0,30);

            $html = '';

            foreach ($installedLang as $insLang) {
                $lang = $transRepo->findOneBy(['productId' => $val->productId, 'productVariantId' => $val->productVariantId, 'marketplaceId' => 1, 'langId' => $insLang->id]);
                if(!is_null($lang)) {
                    if (($lang->description != '<p><br></p>') && ($lang->description != '')){
                        $html .= '<span class="badge">' . $insLang->lang . '</span>';
                    } else {
                        $html .= '<span class="badge badge-red">' . $insLang->lang . '</span>';
                    }
                } else {
                    $html .= '<span class="badge badge-red">' . $insLang->lang . '</span>';
                }
            }

            $response['data'][$i]["DT_RowId"] = 'row__' . $val->productId. '_' . $val->productVariantId;
            $response['data'][$i]["DT_RowClass"] = 'colore';
            $response['data'][$i]['description'] = $desc;
            $response['data'][$i]['lang'] = $html;
            $response['data'][$i]['productId'] = $okManage ? '<a data-toggle="tooltip" title="modifica" data-placement="right" href="'. $modifica . '?productId=' . $val->productId . '&productVariantId=' . $val->productVariantId . '">' . $val->productId. '-' . $val->productVariantId . '</a>' : $val->productId. '-' . $val->productVariantId;

            $i++;
        }

        return json_encode($response);
    }
}