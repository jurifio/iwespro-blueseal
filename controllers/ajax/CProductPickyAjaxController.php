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
class CProductPickyAjaxController extends AAjaxController
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
            $shops = [];
            foreach($val->shop as $shop){
                $shops[] = $shop->title;
            }

            $creationDate = new \DateTime($val->creationDate);

            $response['data'][$i]["DT_RowId"] = 'row__'.$val->id.'__'.$val->productVariant->id;
            $response['data'][$i]["DT_RowClass"] = 'colore';
            $response['data'][$i]['code'] = $okManage ? '<a data-toggle="tooltip" title="modifica" data-placement="right" href="'.$modifica.'?id='.$val->id.'&productVariantId='.$val->productVariantId.'">'.$val->id.'-'.$val->productVariantId.'</a>' : $val->id.'-'.$val->productVariantId;
            $response['data'][$i]['shop'] = implode(',',$shops);

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
            $img = strpos($val->dummyPicture,'s3-eu-west-1.amazonaws.com') ? $val->dummyPicture : $this->urls['dummy']."/".$val->dummyPicture;
            $response['data'][$i]['dummyPicture'] = '<img width="80" src="'.$img.'" />';
            $response['data'][$i]['brand'] = isset($val->productBrand) ? $val->productBrand->name : "";
            $response['data'][$i]['category'] = implode(',<br/>',$cats);
            $response['data'][$i]['tag'] = implode(',',$tags);
            $response['data'][$i]['status'] = $val->productStatus->name;
            $response['data'][$i]['creationDate'] = $creationDate->format('d-m-Y H:i');

            $i++;
        }

        return json_encode($response);
    }
}