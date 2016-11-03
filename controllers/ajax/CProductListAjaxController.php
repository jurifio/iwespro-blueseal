<?php
namespace bamboo\blueseal\controllers\ajax;

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
class CProductListAjaxController extends AAjaxController
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
        $datatable = new CDataTables('vBluesealProductList',['id','productVariantId'],$_GET);
        if(!empty($this->authorizedShops)){
            $datatable->addCondition('shopId',$this->authorizedShops);
        }

        $prodotti = $this->app->repoFactory->create('Product')->em()->findBySql($datatable->getQuery(),$datatable->getParams());
        $count = $this->em->products->findCountBySql($datatable->getQuery(true), $datatable->getParams());
        $totalCount = $this->em->products->findCountBySql($datatable->getQuery('full'), $datatable->getParams());

        $em = $this->app->entityManagerFactory->create('ProductStatus');
        $productStatuses = $em->findAll('limit 99','');

        $statuses = [];
        foreach($productStatuses as $status){
            $statuses[$status->code] = $status->name;
        }

        $modifica = $this->urls['base']."friend/prodotti/modifica";

        $okManage = $this->app->getUser()->hasPermission('/admin/product/edit');

        $response = [];
        $response ['draw'] = $_GET['draw'];
        $response ['recordsTotal'] = $totalCount;
        $response ['recordsFiltered'] = $count;
        $response ['data'] = [];

        /** @var $val CProduct */
        foreach($prodotti as $val){
            $row = [];

            $row["DT_RowId"] = $val->printId();
            $row["DT_RowClass"] = 'colore';

            $row['code'] = $okManage ? '<a data-toggle="tooltip" title="modifica" data-placement="right" href="'.$modifica.'?id='.$val->id.'&productVariantId='.$val->productVariantId.'">'.$val->id.'-'.$val->productVariantId.'</a>' : $val->id.'-'.$val->productVariantId;

            if($val->productPhoto->count() > 3) $imgs = '<br><i class="fa fa-check" aria-hidden="true"></i>';
            else $imgs = "";

            $row['dummy'] = '<a href="#1" class="enlarge-your-img"><img width="50" src="'.$val->getDummyPictureUrl().'" />' . $imgs . '</a>';

            $row['productSizeGroup'] = ($val->productSizeGroup) ? '<span class="small">' . $val->productSizeGroup->locale .  '-' . explode("-", $val->productSizeGroup->macroName)[0] . '</span>' : '';

            $row['details'] = "";
            foreach($val->productSheetActual as $k => $v) {
                if ($trans = $v->productDetail->productDetailTranslation->getFirst()) {
                    $row['details'] .= '<span class="small">' . $trans->name . "</span><br />";
                }
            }

            $row['hasPhotos'] = ($val->productPhoto->count()) ? 'sì' : 'no';
            $row['hasDetails'] = ($val->productSheetActual->count()) ? 'sì' : 'no';

            $row['season'] = '<span class="small">'.$val->productSeason->name . " " . $val->productSeason->year.'</span>';

	        $ext = [];
	        if(isset($val->externalId) && !empty($val->externalId)) {
                $ext[] = $val->externalId;
            }

            $shops = [];
            foreach($val->shopHasProduct as $shp) {
            	$shops[] = $shp->shop->name;
                if (!empty($shp->extId)) {
                    $ext[] = $shp->extId;
                }
	            elseif(!is_null($shp->dirtyProduct)) {
		            if(!empty($shp->dirtyProduct->extId)) {
			            $ext[] = $shp->dirtyProduct->extId;
		            }

		            foreach ($shp->dirtyProduct->dirtySku as $sku) {
			            if (!empty($sku->extSkuId)) {
				            $ext[] = $sku->extSkuId;
			            }
		            }
	            }
            }

            foreach($ext as $kext => $vext) {
                $strExt = '<p><span class="small">';
                $arrVext = str_split($vext, 13);
                $strExt.= implode('<br />', $arrVext);
                $strExt.= '</span></p>';
                $ext[$kext] = $strExt;
            }
            $row['externalId'] = implode('',array_unique($ext));

            $row['cpf'] = $val->printCpf();

            $colorGroup = $val->productColorGroup->getFirst();
            $row['colorGroup'] = '<span class="small">' . (($colorGroup) ? $colorGroup->name : "[Non assegnato]") . '</span>';
            $row['brand'] = isset($val->productBrand) ? $val->productBrand->name : "";
            $row['categoryId'] = '<span class="small">'.$val->getLocalizedProductCategories(" ","<br>").'</span>';
            $row['name'] = $val->productNameTranslation->getFirst()->name;
            $row['tag'] = '<span class="small">'.$val->getLocalizedTags('<br>',false).'</span>';
            $row['status'] = $val->productStatus->name;

            $qty=0;
            $isOnSale = [];
            $shopz = [];
            $mup = [];
            foreach ($val->productSku as $sku) {
                $qty += $sku->stockQty;
                $isOnSale = $sku->isOnSale;
                $iShop = $sku->shop->name;
                if (!in_array($iShop, $shopz)) {
                    $shopz[] = $iShop;

                    $price = ($isOnSale) ? $sku->salePrice : $sku->price;
                    
                    if ((float)$price) {
                        $multiplier = ($val->productSeason->isActive) ? (($isOnSale) ? $sku->shop->saleMultiplier : $sku->shop->currentSeasonMultiplier) : $sku->shop->pastSeasonMultiplier;
                        $value = $sku->value;
                        $friendRevenue = $value + $value * $multiplier / 100;
                        $priceNoVat = $price / 1.22;
                        $mup[] = number_format(($priceNoVat - $friendRevenue) / $priceNoVat * 100, 2, ",", ".");
                    } else {
                        $mup[] = '-';
                    }
                }
            }
            $row['hasQty'] = ($qty) ? 'sì' : 'no';
            $row['hasQty'].= ' - ' . $qty;

            
            $row['shop'] = '<span class="small">';
            $row['shop'] .= implode('<br />',$shops);
            $row['shop'] .= '</span>';
            
            $row['mup'] = '<span class="small">';
            $row['mup'] .= implode('<br />',$mup);
            $row['mup'] .= '</span>';
            
            $row['isOnSale'] = $isOnSale;
            $row['creationDate'] = (new \DateTime($val->creationDate))->format('d-m-Y H:i');

            $response ['data'][] = $row;
        }
        return json_encode($response);
    }
}