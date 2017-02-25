<?php
namespace bamboo\controllers\back\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\core\db\pandaorm\adapter\CMySQLAdapter;
use bamboo\core\db\pandaorm\entities\CEntityManager;
use bamboo\core\intl\CLang;

/**
 * Class CProductActiveListAjaxController.php
 * @package bamboo\app\controllers
 */
class CProductSalesListAjaxController extends AAjaxController
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

        if ($this->app->getUser()->hasPermission('allShops')) {

        } else {
            $res = $this->app->dbAdapter->select('UserHasShop', ['userId' => $this->app->getUser()->getId()])->fetchAll();
            foreach ($res as $val) {
                $this->authorizedShops[] = $val['shopId'];
            }
        }

        $this->em = new \stdClass();
        $this->em->products = $this->app->entityManagerFactory->create('Product');

        return $this->{$action}();
    }

    public function get()
    {

        /** @var $mysql CMySQLAdapter * */
        /** @var $em CEntityManager * */

        $bluesealBase = $this->app->baseUrl(false) . "/blueseal/";
        $dummyUrl = $this->app->cfg()->fetch('paths', 'dummyUrl');

        $sql = "select `p`.`id` AS `id`,`p`.`productVariantId` AS `productVariantId`,`pc`.`slug` AS `slug`,concat(`p`.`id`,'-',`p`.`productVariantId`) AS `code`,group_concat(distinct `Shop`.`title` separator ',') AS `shops`,concat(`p`.`itemno`,' # ',`pv`.`name`) AS `CPF`,concat(`ps`.`name`,' ',`ps`.`year`) AS `season`,`pv`.`name` AS `variant`,`ProductStatus`.`name` AS `status`,`p`.`sortingPriorityId` AS `productPriority`,min(`Tag`.`sortingPriorityId`) AS `tagPriority`,`ProductBrand`.`name` AS `brand`,sum(`ProductSku`.`stockQty`) AS `totalQty`,max(`ProductSku`.`price`) AS `price`,max(`ProductSku`.`salePrice`) AS `salePrice`,cast(max(`pickyshop`.`ProductSku`.`isOnSale`) as char charset utf8) AS `isOnsale` from ((((((((((`Product` `p` join `ProductVariant` `pv`) left join (`ProductHasProductCategory` `phpc` left join `ProductCategory` `pc` on((`phpc`.`productCategoryId` = `pc`.`id`))) on(((`p`.`id` = `phpc`.`productId`) and (`p`.`productVariantId` = `phpc`.`productVariantId`)))) left join `ProductHasTag` on((`pickyshop`.`ProductHasTag`.`productVariantId` = `p`.`productVariantId`))) left join `Tag` on((`pickyshop`.`ProductHasTag`.`tagId` = `pickyshop`.`Tag`.`id`))) join `ProductSku`) join `ProductBrand`) join `ProductStatus`) join `Shop`) left join `ProductHasProductPhoto` on(((`p`.`id` = `pickyshop`.`ProductHasProductPhoto`.`productId`) and (`p`.`productVariantId` = `pickyshop`.`ProductHasProductPhoto`.`productVariantId`)))) join `ProductSeason` `ps` on((`p`.`productSeasonId` = `ps`.`id`))) where ((`pickyshop`.`ProductHasTag`.`productId` = `p`.`id`) and (`p`.`productVariantId` = `pv`.`id`) and (`pickyshop`.`ProductSku`.`shopId` = `pickyshop`.`Shop`.`id`) and (`pickyshop`.`ProductStatus`.`id` = `p`.`productStatusId`) and (`pickyshop`.`ProductStatus`.`code` in ('A','P','I')) and (`p`.`id` = `pickyshop`.`ProductSku`.`productId`) and (`pickyshop`.`ProductSku`.`price` > 0) and (`p`.`productVariantId` = `pickyshop`.`ProductSku`.`productVariantId`) and (`pickyshop`.`ProductBrand`.`id` = `p`.`productBrandId`)) group by `p`.`id`,`p`.`productVariantId` having (`totalQty` >= 0)";
        $datatable = new CDataTables($sql, ['id', 'productVariantId'], $_GET,true);
        if (!empty($this->authorizedShops)) {
            $datatable->addCondition('shopId', $this->authorizedShops);
        }

        $prodotti = $this->app->repoFactory->create('Product')->em()->findBySql($datatable->getQuery(), $datatable->getParams());
        $count = $this->em->products->findCountBySql($datatable->getQuery(true), $datatable->getParams());
        $totlalCount = $this->em->products->findCountBySql($datatable->getQuery('full'), $datatable->getParams());

        $modifica = $bluesealBase . "prodotti/modifica";

        $okManage = $this->app->getUser()->hasPermission('/admin/product/edit');

        $response = [];
        $response ['draw'] = $_GET['draw'];
        $response ['recordsTotal'] = $totlalCount;
        $response ['recordsFiltered'] = $count;
        $response ['aaData'] = [];

        $i = 0;
        foreach ($prodotti as $val) {

            $cats = [];
            foreach($val->productCategoryTranslation as $cat){
                $path = $this->app->categoryManager->categories()->getPath($cat->productCategoryId);
                unset($path[0]);
                $cats[] = '<span>'.implode('/',array_column($path, 'slug')).'</span>';
            }
            $shops = [];
            foreach($val->shop as $shop){
                $shops[] = $shop->title;
            }


            $response['aaData'][$i]["DT_RowId"] = $val->printId();
            //$response['aaData'][$i]["code"] = $val->id . '-' . $val->productVariantId;
            $response['aaData'][$i]['code'] = ($okManage) ? '<a data-toggle="tooltip" title="modifica" data-placement="right" href="'.$modifica.'?id='.$val->id.'&productVariantId='.$val->productVariantId.'">'.$val->id.'-'.$val->productVariantId.'</a>' : $val->id.'-'.$val->productVariantId;
            $response['aaData'][$i]["brand"] = isset($val->productBrand) ? $val->productBrand->name : "";
            $response['aaData'][$i]["slug"] = '<span class="small">' . implode(", <br />", $cats) . '</span><br />';
            $th = "";
            $tr = "";
            $res = $this->app->dbAdapter->query("SELECT s.name, sum(ps.stockQty) stock
                                          FROM ProductSku ps , ProductSize s
                                          WHERE ps.productSizeId = s.id AND
                                              ps.productId = ? AND
                                              ps.productVariantId = ?
                                          GROUP BY ps.productSizeId
                                          HAVING stock > 0 ORDER BY `name`", [$val->id, $val->productVariantId])->fetchAll();
            foreach ($res as $sums) {
                $th .= "<th>" . $sums['name'] . "</th>";
                $tr .= "<td>" . $sums['stock'] . "</td>";
            }
            $response['aaData'][$i]["slug"] .= '<table class="nested-table"><thead><tr>'.$th . "</tr></thead><tbody>" . $tr . "</tbody></table>";
            $response['aaData'][$i]['season'] = $val->productSeason->name . " " . $val->productSeason->year;
            $response['aaData'][$i]["dummyPicture"] = '<img width="80" src="' . $val->getDummyPictureUrl() . '">';
            $response['aaData'][$i]['CPF'] = $val->itemno.' # '.$val->productVariant->name;
            $response['aaData'][$i]['variant'] = $val->productVariant->name;

            //$response['aaData'][$i]["skus"] = '<table class="nested-table"><thead><tr>'.$th . "</tr></thead><tbody>" . $tr . "</tbody></table>";

            $res = $this->app->dbAdapter->query("SELECT max(ps.price) as price,  max(ps.saleprice) as sale, p.isOnSale, ps.value as val, /*group_concat(distinct s.title SEPARATOR ',')*/ s.name as shop
                                          FROM Product p, ProductSku ps, Shop s
                                          WHERE 
                                          p.id = ps.productId and 
                                          p.productVariantId = ps.productVariantId and 
                                          ps.shopId = s.id AND
                                              p.id= ? AND
                                              p.productVariantId = ?
                                          GROUP BY p.id, p.productVariantId, s.id", [$val->id, $val->productVariantId])->fetchAll();


            $response['aaData'][$i]["price"] = '<span class="small">';
            $response['aaData'][$i]["sale"] = '<span class="small">';
            $response['aaData'][$i]["percentage"] = '<span class="small">';
            $response['aaData'][$i]["shops"] = '<span class="small">';
            $response['aaData'][$i]["friendRevenue"] = '<span class="small">';
            $response['aaData'][$i]["friendSaleRevenue"] = '<span class="small">';
            $response['aaData'][$i]["friendPreRevenue"] = '<span class="small">';
            /*
            $response['aaData'][$i]["price"] = '<span>';
            $response['aaData'][$i]["sale"] = '<span>';
            $response['aaData'][$i]["percentage"] = '<span>';
            $response['aaData'][$i]["shops"] = '<span>';
            $response['aaData'][$i]["friendRevenue"] = '<span>';
            $response['aaData'][$i]["friendSaleRevenue"] = '<span>';
            $response['aaData'][$i]["friendPreRevenue"] = '<span>';
            */


            // Si, lo so sta robba è un zozzo, sto a mette pezze in corsa

            foreach($res as $v) {
                $friendMargin = '';
                $friendSaleMargin = '';
                $friendPastMargin = '';

                $shopRepo = $this->app->repoFactory->create("Shop")->findOneBy(['name' => $v['shop']]);
                if ($v['val']) {
                    $friendRevenue = $v['val'] + $v['val'] * $shopRepo->currentSeasonMultiplier / 100;
                    $friendSaleRevenue = $v['val'] + $v['val'] * $shopRepo->saleMultiplier / 100;
                    $pastSeasonRevenue = $v['val'] + $v['val'] * $shopRepo->pastSeasonMultiplier / 100;

                    $priceNoVAT = ($v['price']) ? $v['price'] / 1.22 : 0;
                    $salePriceNoVAT = ($v['sale']) ? $v['sale'] / 1.22 : 0;

                    if ($val->productSeason->isActive) {
                        if ($res[0]['isOnSale']) {
                            if (!$salePriceNoVAT) $friendSaleMargin = ' | -';
                            else {
                                $friendSaleMargin = ' | <span style="font-weight: bold;" >' .
                                    $this->formatPrice(($salePriceNoVAT - $friendSaleRevenue) / $salePriceNoVAT * 100) . '%</span>';
                            }
                        }
                        else {
                            if (!$priceNoVAT) $friendMargin = ' | -';
                            else {
                                $friendMargin = ' | <span style="font-weight: bold;" >' . $this->formatPrice(($priceNoVAT - $friendRevenue) / $priceNoVAT * 100) . '%</span>';
                            }
                        }
                    } else {
                        if ($res[0]['isOnSale']) {
                            if (!$salePriceNoVAT) {
                                $friendPastMargin = ' | -';
                            } else {
                                $friendPastMargin = ' | <span style="font-weight: bold;" >' .
                                    $this->formatPrice(($salePriceNoVAT - $pastSeasonRevenue) / $salePriceNoVAT * 100) . '%</span>';
                            }
                        }
                        else {
                            if (!$priceNoVAT) {
                                $friendPastMargin = ' | -';
                            } else {
                                $friendPastMargin = ' | <span style="font-weight: bold;">' . $this->formatPrice(($priceNoVAT - $pastSeasonRevenue) / $priceNoVAT * 100) . '%</span>';
                            }
                        }
                    }
                } else {
                    $friendMargin = ' | -';
                    $friendSaleMargin = ' | -';
                    $friendPastMargin = ' | -';
                }

                $response['aaData'][$i]["price"] .= $this->formatPrice($v['price']) . " | " . $this->formatPrice($v['val']) . "<br />";
                $styleStart = ($v['isOnSale']) ? '<span style="color: #992222; font-weight: bold">' : '';
                $styleEnd = ($v['isOnSale']) ? '</span>' : '';
                $response['aaData'][$i]["sale"] .=  $styleStart . $this->formatPrice($v['sale']) . $styleEnd . "<br />";
                $response['aaData'][$i]["percentage"] .= ($res[0]['sale']) ? floor(100 - 100 / ($res[0]['price'] / $res[0]['sale'])) . '%' . "<br />" : '-';
                $response['aaData'][$i]["shops"] .= $v['shop'] . "<br />";
                $response['aaData'][$i]['isOnSale'] = '<span class="small">' . $v['isOnSale'] . '</span>';
                $response['aaData'][$i]["friendRevenue"] .= (isset($friendRevenue)) ? $this->formatPrice( $friendRevenue ) . " | " . $shopRepo->currentSeasonMultiplier . $friendMargin . "<br />" : "-<br />";
                $response['aaData'][$i]["friendSaleRevenue"] .= (isset($friendSaleRevenue)) ? $this->formatPrice( $friendSaleRevenue ) . " | " . $shopRepo->saleMultiplier. $friendSaleMargin . "<br />" : "-<br />";
                $response['aaData'][$i]["friendPreRevenue"] .= (isset($pastSeasonRevenue)) ? $this->formatPrice( $pastSeasonRevenue ) . " | " . $shopRepo->pastSeasonMultiplier . $friendPastMargin . "<br />" : "-<br />";
            }

            $response['aaData'][$i]["price"] .= '</span>';
            $response['aaData'][$i]["sale"] .= '</span>';
            $response['aaData'][$i]["percentage"] .= '</span>';
            $response['aaData'][$i]["shops"] .= '</span>';
            $response['aaData'][$i]["friendRevenue"] .= '</span>';
            $response['aaData'][$i]["friendSaleRevenue"] .= '</span>';
            $response['aaData'][$i]["friendPreRevenue"] .= '</span>';

            $i++;
        }
        return json_encode($response);
    }
    public function post()
    {
        throw new \Exception();
    }

    public function delete()
    {
        throw new \Exception();
    }
    private function formatPrice($val) {
        return str_replace(".", ",",
            floor($val * 100) / 100
        );
    }
}