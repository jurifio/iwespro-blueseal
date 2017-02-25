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
class CProductFusionListAjaxController extends AAjaxController
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

        $sql = "select `p`.`id` AS `id`,`p`.`productVariantId` AS `productVariantId`,concat(`p`.`id`,'-',`p`.`productVariantId`) AS `code`,group_concat(distinct `Shop`.`title` separator ',') AS `shops`,concat(`p`.`itemno`,' # ',`pv`.`name`) AS `cpf`,`pv`.`name` AS `variant`,`Tag`.`id` AS `tag`,`ProductStatus`.`name` AS `status`,`p`.`sortingPriorityId` AS `productPriority`,min(`Tag`.`sortingPriorityId`) AS `tagPriority`,`ProductBrand`.`name` AS `brand`,sum(`ProductSku`.`stockQty`) AS `totalQty`,`p`.`creationDate` AS `creation` from ((((((((`Product` `p` join `ProductVariant` `pv`) join `Tag`) join `ProductSku`) join `ProductHasTag`) join `ProductBrand`) join `ProductStatus`) join `Shop`) left join `ProductHasProductPhoto` on(((`p`.`id` = `ProductHasProductPhoto`.`productId`) and (`p`.`productVariantId` = `ProductHasProductPhoto`.`productVariantId`)))) where ((`ProductHasTag`.`productId` = `p`.`id`) and (`p`.`productVariantId` = `pv`.`id`) and (`ProductSku`.`shopId` = `Shop`.`id`) and (`ProductHasTag`.`productVariantId` = `p`.`productVariantId`) and (`ProductStatus`.`id` = `p`.`productStatusId`) and (`ProductStatus`.`code` in ('A','P','I')) and (`ProductHasTag`.`tagId` = `Tag`.`id`) and (`p`.`id` = `ProductSku`.`productId`) and (`ProductSku`.`price` > 0) and (`p`.`productVariantId` = `ProductSku`.`productVariantId`) and (`ProductBrand`.`id` = `p`.`productBrandId`)) group by `p`.`id`,`p`.`productVariantId`,`ProductSku`.`price` having (`totalQty` >= 0)";
        $datatable = new CDataTables($sql, ['id', 'productVariantId'], $_GET,true);
        /*if (!empty($this->authorizedShops)) {
            $datatable->addCondition('shopId', $this->authorizedShops);
        }*/

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
            $shops = [];
            foreach ($val->shop as $shop) {
                $shops[] = $shop->name;
            }
            $response['aaData'][$i]["DT_RowId"] = $val->id . '-' . $val->productVariant->id;
            $response['aaData'][$i]["code"] = $val->id . '-' . $val->productVariantId;
            $response['aaData'][$i]['code'] = ($okManage) ? '<a data-toggle="tooltip" title="modifica" data-placement="right" href="'.$modifica.'?id='.$val->id.'&productVariantId='.$val->productVariantId.'">'.$val->id.'-'.$val->productVariantId.'</a>' : $val->id.'-'.$val->productVariantId;
            $response['aaData'][$i]["brand"] = isset($val->productBrand) ? $val->productBrand->name : "";
            $response['aaData'][$i]["dummyPicture"] = '<img width="80" src="' . $val->getDummyPictureUrl() . '">';
            $response['aaData'][$i]["status"] = $val->productStatus->name;
            $response['aaData'][$i]['CPF'] = $val->itemno.' # '.$val->productVariant->name;
            $response['aaData'][$i]['variant'] = $val->productVariant->name;
            $response['aaData'][$i]['sizeGroup'] = '<span class="small">' . $val->productSizeGroup->locale .  '-' . explode("-", $val->productSizeGroup->macroName)[0] . '</span>';
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
            $response['aaData'][$i]["skus"] = '<table class="nested-table"><thead><tr>'.$th . "</tr></thead><tbody>" . $tr . "</tbody></table>";

            $res = $this->app->dbAdapter->query("SELECT max(ps.price) as price,  group_concat(distinct s.title SEPARATOR ',') as shops
                                          FROM ProductSku ps, Shop s
                                          WHERE ps.shopId = s.id AND
                                              ps.productId = ? AND
                                              ps.productVariantId = ?
                                          GROUP BY ps.productId, ps.productVariantId", [$val->id, $val->productVariantId])->fetchAll();


            $response['aaData'][$i]["price"] = isset($res[0]) ? $res[0]['price'] : 0;
            $response['aaData'][$i]["shops"] = isset($res[0]) ? $res[0]['shops'] : 0;

            $res = $this->app->dbAdapter->query("SELECT sum(ol.activePrice) incasso, count(DISTINCT ol.id) conto
                                          FROM OrderLine ol, `Order` o
                                          WHERE o.status LIKE 'ORD%' AND
                                              o.id = ol.orderId AND
                                              ol.productId = ? AND
                                              ol.productVariantId = ?
                                          GROUP BY ol.productId, ol.productVariantId", [$val->id, $val->productVariantId])->fetchAll();

            $response['aaData'][$i]["income"] = isset($res[0]) ? $res[0]['incasso'] : 0;
            $response['aaData'][$i]["sells"] = isset($res[0]) ? $res[0]['conto'] : 0;


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
}