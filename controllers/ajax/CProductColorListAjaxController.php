<?php
namespace bamboo\blueseal\controllers\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\core\intl\CLang;


/**
 * Class CProductDetailListAjaxController
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
class CProductColorListAjaxController extends AAjaxController
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
        $this->app->setLang(new CLang(1, 'it'));
        $this->urls['base'] = $this->app->baseUrl(false) . "/blueseal/";
        $this->urls['page'] = $this->urls['base'] . "prodotti";
        $this->urls['dummy'] = $this->app->cfg()->fetch('paths', 'dummyUrl');

        $this->em = new \stdClass();
        $this->em->products = $this->app->entityManagerFactory->create('Product');

        return $this->{$action}();
    }

    public function get()
    {
        $sql = "SELECT
                  concat(`p`.`id`, '-', `p`.`productVariantId`)     AS `code`,
                  `p`.`id`                                          AS `id`,
                  `p`.`productVariantId`                            AS `productVariantId`,
                  `pv`.`description`                                AS `colorName`,
                  `pcg`.`name`                                      AS `colorGroupName`,
                  `p`.`dummyPicture`                                AS `dummyPicture`,
                  `dp`.`var`                                        AS `var`,
                  group_concat(DISTINCT `pc`.`id` SEPARATOR ',')    AS `catId`,
                  group_concat(DISTINCT `pct`.`name` SEPARATOR ',') AS `categories`,
                  sum(`ps`.`stockQty`)                              AS `stock`
                FROM `Product` `p`
                  JOIN `ProductVariant` `pv` on `p`.`productVariantId` = `pv`.`id`
                  JOIN `DirtyProduct` `dp` on (`p`.`id` = `dp`.`productId`) AND (`p`.`productVariantId` = `dp`.`productVariantId`)
                  JOIN `ProductSku` `ps` on (`p`.`id` = `ps`.`productId`) AND (`p`.`productVariantId` = `ps`.`productVariantId`) 
                  LEFT JOIN (`ProductHasProductCategory` `phpc` 
                              JOIN `ProductCategory` `pc` ON `phpc`.`productCategoryId` = `pc`.`id` 
                              JOIN `ProductCategoryTranslation` `pct` on (`pc`.`id` = `pct`.`productCategoryId`)  
                              ) 
                        ON (`p`.`id` = `phpc`.`productId`) AND `p`.`productVariantId` = `phpc`.`productVariantId`
                  LEFT JOIN `ProductColorGroup` `pcg` ON `p`.`productColorGroupId` = `pcg`.`id`
                WHERE (`pct`.`langId` = 1)
                GROUP BY `p`.`id`, `p`.`productVariantId`
                HAVING (`stock` > 0);";

        $datatable = new CDataTables($sql, ['id', 'productVariantId'], $_GET);

        $products = $this->app->repoFactory->create('Product')->em()->findBySql($datatable->getQuery(), $datatable->getParams());
        $count = $this->em->products->findCountBySql($datatable->getQuery(true), $datatable->getParams());
        $totalCount = $this->em->products->findCountBySql($datatable->getQuery('full'), $datatable->getParams());


        $response = [];
        $response ['draw'] = $_GET['draw'];
        $response ['recordsTotal'] = $totalCount;
        $response ['recordsFiltered'] = $count;
        $response ['data'] = [];

        $i = 0;

        foreach ($products as $v) {
            try {
                $response['data'][$i]["DT_RowId"] = 'row__' . $v->id;
                $response['data'][$i]["DT_RowClass"] = 'colore';
                $response['data'][$i]['code'] = '<a href="/blueseal/prodotti/modifica?id=' . $v->id . '&productVariantId=' . $v->productVariantId . '">' . $v->id . '-' . $v->productVariantId . '</a>';
                $response['data'][$i]['colorName'] = $v->productVariant->description;
                $colorGroupCollection = $v->productColorGroup->productColorGroupTranslation->getFirst();
                $response['data'][$i]['colorGroupName'] = ($colorGroupCollection) ? $colorGroupCollection->name : '-';
                $response['data'][$i]['dummyPic'] = '<img width="80" src="' . $v->getDummyPictureUrl() . '">';
                $response['data'][$i]['categorie'] = '';

                foreach ($v->productCategory as $cat) {
                    $res = $this->app->dbAdapter->query("SELECT replace(group_concat(name), ',', '/') AS name FROM `ProductCategory` AS `pc` JOIN `ProductCategoryTranslation` AS `pct` WHERE `pc`.`id` = `pct`.productCategoryId AND `pc`.`lft` <= ? AND `pc`.`rght` >= ? AND pct.langId = 1 ORDER BY `pc`.`lft` ASC"
                        , [$cat->lft, $cat->rght])->fetchAll();

                    $response['data'][$i]['categorie'] .= '<span style="font-size: 0.8em">' . $res[0]['name'] . '</span><br />';
                }
                $res = $this->app->repoFactory->create("DirtyProduct")->em()->findOneBy(['productId' => $v->id, 'productVariantId' => $v->productVariantId]);
                $response['data'][$i]['var'] = $res->var;
                $response['data'][$i]['stato'] = $v->productStatus->name;
                $i++;
            } catch (\Throwable $e) {
                throw $e;
            }
        }
        return json_encode($response);
    }
}