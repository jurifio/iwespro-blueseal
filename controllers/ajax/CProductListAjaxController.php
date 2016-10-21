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

        $modifica = $this->urls['base']."friend/prodotti/modifica";

        $okManage = $this->app->getUser()->hasPermission('/admin/product/edit');

        $response = [];
        $response ['draw'] = $_GET['draw'];
        $response ['recordsTotal'] = $totalCount;
        $response ['recordsFiltered'] = $count;
        $response ['data'] = [];

        $i = 0;

        foreach($prodotti as $val){

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

            $response['data'][$i]["DT_RowId"] = $val->printId();
            $response['data'][$i]["DT_RowClass"] = 'colore';
            $response['data'][$i]['code'] = $okManage ? '<a data-toggle="tooltip" title="modifica" data-placement="right" href="'.$modifica.'?id='.$val->id.'&productVariantId='.$val->productVariantId.'">'.$val->id.'-'.$val->productVariantId.'</a>' : $val->id.'-'.$val->productVariantId;

            //$response['data'][$i]['sizeGroup'] = ($val->productSizeGroup) ? '<span class="small">' . $val->productSizeGroup->locale .  '-' . explode("-", $val->productSizeGroup->macroName)[0] . '</span>' : '';
            $img = strpos($val->dummyPicture,'amazonaws') ? $val->dummyPicture : $this->urls['dummy']."/".$val->dummyPicture;
            if($val->productPhoto->count() > 3) $imgs = '<br><i class="fa fa-check" aria-hidden="true"></i>';
            else $imgs = "";
            //$response['data'][$i]['dummyPicture'] = '<img width="60" src="'.$img.'" />'.$imgs;
            $response['data'][$i]['details'] = '<a href="#1" class="enlarge-your-img"><img width="50" src="'.$img.'" />' . $imgs . '</a><br />';
            $response['data'][$i]['details'] .= ($val->productSizeGroup) ? '<span class="small">' . $val->productSizeGroup->locale .  '-' . explode("-", $val->productSizeGroup->macroName)[0] . '</span><br />' : '';
            $details = $this->app->repoFactory->create('ProductSheetActual')->em()->findBy(['productId' => $val->id, 'productVariantId' => $val->productVariantId]);
            foreach($details as $k => $v) {
                if ($trans = $v->productDetail->productDetailTranslation->getFirst()) {
                    $response['data'][$i]['details'] .= '<span class="small">' . $trans->name . "</span><br />";
                }
            }

            $response['data'][$i]['season'] = '<span class="small">';
            $response['data'][$i]['season'] .= $val->productSeason->name . " " . $val->productSeason->year;
            $response['data'][$i]['season'] .= '</span>';
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
                $strExt = '<p>';
                $arrVext = str_split($vext, 13);
                $strExt.= implode('<br />', $arrVext);
                $strExt.= '</p>';
                $ext[$kext] = $strExt;
            }

	        $ext = implode('',array_unique($ext));

	        $tags = [];
	        foreach ($val->tag as $tag) $tags[] = $tag->getLocalizedName();

            $response['data'][$i]['externalId'] = '<span class="small">';
            $response['data'][$i]['externalId'] .= empty($ext) ? "" : $ext;
            $response['data'][$i]['externalId'] .= '</span>';

            $cpf = '<span class="small">';
            $cpf.= (14 > strlen($val->itemno.' # '.$val->productVariant->name)) ?
                $val->itemno.' # '.$val->productVariant->name :
                substr($val->itemno.' # '.$val->productVariant->name, 0, 13) . '&hellip;';
            $cpf.='</span>';
            $response['data'][$i]['cpf'] = $cpf;

            $colorGroup = $val->productColorGroup->getFirst();
            $response['data'][$i]['colorGroup'] = ($colorGroup) ? $colorGroup->name : "[Non assegnato]";

            $response['data'][$i]['brand'] = isset($val->productBrand) ? $val->productBrand->name : "";
            $response['data'][$i]['slug'] = '<span class="small">';
            $response['data'][$i]['slug'] .= implode(',<br/>',$cats); //category
            $response['data'][$i]['slug'] .= $nameInCats;
            $response['data'][$i]['slug'] .= '</span>';
            $response['data'][$i]['tag'] = '<span class="small">';
            $response['data'][$i]['tag'] .= implode(',<br />',$tags);
            $response['data'][$i]['tag'] .= '</span>';
            $response['data'][$i]['status'] = $val->productStatus->name;

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
            $response['data'][$i]['available'] = ($qty) ? 'sì' : 'no';
            $response['data'][$i]['available'].= ' - ' . $qty;

            
            $response['data'][$i]['shop'] = '<span class="small">';
            $response['data'][$i]['shop'] .= implode('<br />',$shops);
            $response['data'][$i]['shop'] .= '</span>';
            
            $response['data'][$i]['mup'] = '<span class="small">';
            $response['data'][$i]['mup'] .= implode('<br />',$mup);
            $response['data'][$i]['mup'] .= '</span>';
            
            $response['data'][$i]['isOnSale'] = $isOnSale;
            $response['data'][$i]['creationDate'] = $creationDate->format('d-m-Y H:i');

            $i++;
        }

        return json_encode($response);
    }
}