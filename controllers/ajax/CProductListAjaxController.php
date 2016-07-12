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
	    $datatable->addSearchColumn('extId');
	    $datatable->addSearchColumn('extSkuId');

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

            $creationDate = new \DateTime($val->creationDate);

            $response['data'][$i]["DT_RowId"] = 'row__'.$val->id.'__'.$val->productVariant->id;
            $response['data'][$i]["DT_RowClass"] = 'colore';
            $response['data'][$i]['code'] = $okManage ? '<a data-toggle="tooltip" title="modifica" data-placement="right" href="'.$modifica.'?id='.$val->id.'&productVariantId='.$val->productVariantId.'">'.$val->id.'-'.$val->productVariantId.'</a>' : $val->id.'-'.$val->productVariantId;

            $response['data'][$i]['sizeGroup'] = ($val->productSizeGroup) ? '<span class="small">' . $val->productSizeGroup->locale .  '-' . explode("-", $val->productSizeGroup->macroName)[0] . '</span>' : '';
            $response['data'][$i]['details'] = '';

            $details = $this->app->repoFactory->create('ProductSheetActual')->em()->findBy(['productId' => $val->id, 'productVariantId' => $val->productVariantId]);
            foreach($details as $k => $v) {
                if ($trans = $v->productDetail->productDetailTranslation->getFirst()) {
                    $response['data'][$i]['details'] .= '<span class="small">' . $trans->name . "</span><br />";
                }
            }

            $response['data'][$i]['season'] = $val->productSeason->name . " " . $val->productSeason->year;
	        $ext = [];
	        if (!is_null($val->shopHasProduct) && !empty($val->shopHasProduct->extId)) {
		        $ext[] = $val->shopHasProduct->extId;
	        }
	        if(isset($val->externalId)) {
		        $ext[] = $val->externalId;
	        }
	        if(!is_null($val->shopHasProduct) && !is_null($val->shopHasProduct->dirtyProduct)) {
		        if(!empty($val->shopHasProduct->dirtyProduct->extId)) {
			        $ext[] = $val->shopHasProduct->dirtyProduct->extId;
		        }
		        if(!is_null($val->shopHasProduct->dirtyProduct->dirtySku)) {
			        foreach ($val->shopHasProduct->dirtyProduct->dirtySku as $sku) {
				        if(!empty($sku->extSkuId)) {
					        $ext[] = $sku->extSkuId;
				        }
			        }
		        }

	        }
	        $ext = implode('<br>',array_unique($ext));

	        $tags = [];
	        foreach ($val->tag as $tag) $tags[] = $tag->tagTranslation->getFirst()->name;

            $response['data'][$i]['externalId'] = empty($ext) ? "" : $ext;
            $response['data'][$i]['cpf'] = $val->itemno.' # '.$val->productVariant->name;

            $colorGroup = $val->productColorGroup->getFirst();
            $response['data'][$i]['colorGroup'] = ($colorGroup) ? $colorGroup->name : "[Non assegnato]";
            $img = strpos($val->dummyPicture,'s3-eu-west-1.amazonaws.com') ? $val->dummyPicture : $this->urls['dummy']."/".$val->dummyPicture;
	        if($val->productPhoto->count() > 3) $imgs = '<br><i class="fa fa-check" aria-hidden="true"></i>';
	        else $imgs = "";
            $response['data'][$i]['dummyPicture'] = '<img width="80" src="'.$img.'" />'.$imgs;
            $response['data'][$i]['brand'] = isset($val->productBrand) ? $val->productBrand->name : "";
            $response['data'][$i]['slug'] = implode(',<br/>',$cats); //category
            $response['data'][$i]['tag'] = implode(',',$tags);
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
                    
                    $price = ($isOnSale) ? $sku->price : $sku->salePrice;
                    $multiplier = ($val->productSeason->isActive) ? (($isOnSale) ? $sku->shop->saleMultiplier : $sku->shop->currentSeasonMultiplier) : $sku->shop->pastSeasonMultiplier;
                    $friendRevenue = ($price) ? $price + $price * $multiplier / 100 : 0;
                    $priceNoVat = $price / 1.22;
                    $mup[] = number_format(($priceNoVat - $friendRevenue) / $priceNoVat * 100, 2, ",", ".");
                }
            }
            $response['data'][$i]['available'] = ($qty) ? 'sì' : 'no';
            $response['data'][$i]['available'].= ' - ' . $qty;
            
            
            $response['data'][$i]['shop'] = '<span class="small">';
            $response['data'][$i]['shop'] .= implode('<br />',$shopz);
            $response['data'][$i]['shop'] .= '</span>';
            
            $response['data'][$i]['mup'] = '<span class="small">';
            $response['data'][$i]['mup'] .= implode('<br /> ',$mup);
            $response['data'][$i]['mup'] .= '</span>';
            
            $response['data'][$i]['isOnSale'] = $isOnSale;
            $response['data'][$i]['creationDate'] = $creationDate->format('d-m-Y H:i');

            $i++;
        }

        return json_encode($response);
    }
}