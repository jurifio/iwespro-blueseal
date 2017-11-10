<?php
namespace bamboo\controllers\back\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\core\intl\CLang;
use bamboo\domain\entities\CProduct;

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
class CProductTagListAjaxController extends AAjaxController
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
        $this->em->products = $this->app->entityManagerFactory->create('Product');

        return $this->{$action}();
    }

    public function get()
    {
        $sql = "SELECT
                  concat(`p`.`id`, '-', `pv`.`id`)                AS `code`,
                  `p`.`id`                                        AS `id`,
                  `p`.`productVariantId`                          AS `productVariantId`,
                  concat(`pse`.`name`, ' ', `pse`.`year`)         AS `season`,
                  `pse`.`isActive`                                AS `isActive`,
                  `s`.`name`                                      AS `shop`,
                  `p`.`sortingPriorityId`                         AS `priority`,
                  `pb`.`name`                                     AS `brand`,
                  `ps`.`name`                                     AS `status`,
                  `p`.`creationDate`                              AS `creationDate`,
                  group_concat(DISTINCT `t`.`slug` SEPARATOR ',') AS `tag`,
                  `pcg`.`name`                                    AS `colorGroup`,
                  `p`.`isOnSale`                                AS `isOnSale`,
                  if(((SELECT sum(`psk`.`stockQty`) AS `summ`
                       FROM `ProductSku` `psk`
                       WHERE ((`psk`.`productId` = `p`.`id`) AND (`psk`.`productVariantId` = `p`.`productVariantId`))) > 0), 'sì',
                     'no')                                        AS `available`
                FROM ((((((((((`Product` `p`
                  JOIN `ProductSeason` `pse` ON ((`p`.`productSeasonId` = `pse`.`id`))) JOIN `ProductVariant` `pv`
                    ON ((`p`.`productVariantId` = `pv`.`id`))) JOIN `ProductBrand` `pb` ON ((`p`.`productBrandId` = `pb`.`id`))) JOIN
                  `ProductStatus` `ps` ON ((`ps`.`id` = `p`.`productStatusId`))) JOIN `ShopHasProduct` `sp`
                    ON (((`p`.`id` = `sp`.`productId`) AND (`p`.`productVariantId` = `sp`.`productVariantId`)))) JOIN `Shop` `s`
                    ON ((`s`.`id` = `sp`.`shopId`))) LEFT JOIN `ProductSku` `psk`
                    ON (((`p`.`id` = `psk`.`productId`) AND (`p`.`productVariantId` = `psk`.`productVariantId`)))) LEFT JOIN
                  `ProductHasTag` `pht`
                    ON (((`pht`.`productId` = `p`.`id`) AND (`pht`.`productVariantId` = `p`.`productVariantId`)))) LEFT JOIN `Tag` `t`
                    ON ((`pht`.`tagId` = `t`.`id`))) LEFT JOIN
                  `ProductColorGroup` `pcg` ON ((`p`.`productColorGroupId` = `pcg`.`id`)))
                WHERE ((`pcg`.`langId` = 1) AND (`ps`.`code` IN ('A', 'P', 'I')))
                GROUP BY `p`.`productVariantId`";
        $datatable = new CDataTables($sql,['id','productVariantId'],$_GET,true);
        if(!empty($this->authorizedShops)){
            $datatable->addCondition('shopId',$this->authorizedShops);
        }
	    //$datatable->addSearchColumn('extId');
	    //$datatable->addSearchColumn('extSkuId');

        $prodotti = $this->app->repoFactory->create('Product')->em()->findBySql($datatable->getQuery(),$datatable->getParams());
        $count = $this->em->products->findCountBySql($datatable->getQuery(true), $datatable->getParams());
        $totalCount = $this->em->products->findCountBySql($datatable->getQuery('full'), $datatable->getParams());

        $em = $this->app->entityManagerFactory->create('ProductStatus');
        $productStatuses = $em->findAll('limit 99','');

        $statuses = [];
        foreach($productStatuses as $status){
            $statuses[$status->code] = $status->name;
        }

        $modifica = $this->urls['base']."prodotti/modifica";

        $okManage = $this->app->getUser()->hasPermission('/admin/product/edit');

        $response = [];
        $response ['draw'] = $_GET['draw'];
        $response ['recordsTotal'] = $totalCount;
        $response ['recordsFiltered'] = $count;
        $response ['data'] = [];

        $i = 0;

        foreach($prodotti as $val){

            /** @var CProduct $val */
            $cats = [];
            foreach($val->productCategoryTranslation as $cat){
                $path = $this->app->categoryManager->categories()->getPath($cat->productCategoryId);
                unset($path[0]);
                $cats[] = '<span>'.implode('/',array_column($path, 'slug')).'</span>';
            }
            /*$shops = [];
            foreach($val->shop as $shop){
                $shops[] = $shop->title;
            }*/
            $nameInCats = '';
            foreach($val->productNameTranslation as $v) {
                if (1 == $v->langId) {
                    $nameInCats = '<br /><strong>nome:</strong>' . $v->name . '<br />';
                    break;
                }
            }

            $creationDate = new \DateTime($val->creationDate);

            $response['data'][$i]["DT_RowId"] = 'row__'.$val->id.'__'.$val->productVariant->id;
            $response['data'][$i]["DT_RowClass"] = 'colore';
            $response['data'][$i]['code'] = $okManage ? '<a data-toggle="tooltip" title="modifica" data-placement="right" href="'.$modifica.'?id='.$val->id.'&productVariantId='.$val->productVariantId.'">'.$val->id.'-'.$val->productVariantId.'</a>' : $val->id.'-'.$val->productVariantId;

            if($val->productPhoto->count() > 3) $imgs = '<br><i class="fa fa-check" aria-hidden="true"></i>';
            else $imgs = "";

            $response['data'][$i]['details'] = '<img width="50" src="'.$val->getDummyPictureUrl().'" />' . $imgs . '<br />';
            $response['data'][$i]['details'] .= ($val->productSizeGroup) ? '<span class="small">' . $val->productSizeGroup->locale .  '-' . explode("-", $val->productSizeGroup->productSizeMacroGroup->name)[0] . '</span><br />' : '';
            $details = $this->app->repoFactory->create('ProductSheetActual')->em()->findBy(['productId' => $val->id, 'productVariantId' => $val->productVariantId]);
            foreach($details as $k => $v) {
                if ($trans = $v->productDetail->productDetailTranslation->getFirst()) {
                    $response['data'][$i]['details'] .= '<span class="small">' . $trans->name . "</span><br />";
                }
            }

            $response['data'][$i]['season'] = '<span class="small">';
            $response['data'][$i]['season'] .= $val->productSeason->name . " " . $val->productSeason->year;
            $response['data'][$i]['season'] .= '</span>';

	        $tags = [];
	        foreach ($val->tag as $tag) $tags[] = $tag->getLocalizedName();


            $colorGroup = $val->productColorGroup->productColorGroupTranslation->getFirst();
            $response['data'][$i]['colorGroup'] = ($colorGroup) ? $colorGroup->name : "[Non assegnato]";

            $response['data'][$i]['brand'] = isset($val->productBrand) ? $val->productBrand->name : "";
            $response['data'][$i]['priority'] = $val->sortingPriorityId;
            $response['data'][$i]['tag'] = '<span class="small">';
            $response['data'][$i]['tag'] .= implode(',<br />',$tags);
            $response['data'][$i]['tag'] .= '</span>';
            $response['data'][$i]['status'] = $val->productStatus->name;

            $shopz = [];
            $isOnSale = $val->isOnSale();
            $stock = 0;
            foreach ($val->productSku as $sku) {
                $iShop = $sku->shop->name;
                if (!in_array($iShop, $shopz)) {
                    $shopz[] = $iShop;
                }
                $stock+= $sku->stockQty;
            }
            $available = ($stock) ? 'sì' : 'no';

            $response['data'][$i]['isOnSale'] = $isOnSale;
            $response['data'][$i]['available'] = $available . (($stock) ? ": " . $stock : '');

            $response['data'][$i]['shop'] = '<span class="small">';
            $response['data'][$i]['shop'] .= implode('<br />',$shopz);
            $response['data'][$i]['shop'] .= '</span>';

            $i++;
        }
        return json_encode($response);
    }
}