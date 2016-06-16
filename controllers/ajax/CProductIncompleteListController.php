<?php
namespace bamboo\blueseal\controllers\ajax;

use bamboo\domain\entities\CProduct;
use bamboo\blueseal\business\CDataTables;
use bamboo\core\intl\CLang;
use bamboo\core\db\pandaorm\entities\CEntityManager;
use bamboo\core\db\pandaorm\adapter\CMySQLAdapter;

/**
 * Class CProductIncompleteListController.php
 * @package bamboo\app\controllers
 */
class CProductIncompleteListController extends AAjaxController
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

        /** @var $mysql CMySQLAdapter **/
        /** @var $em CEntityManager **/

        $bluesealBase = $this->app->baseUrl(false)."/blueseal/";
        $dummyUrl = $this->app->cfg()->fetch('paths','dummyUrl');

        $datatable = new CDataTables('vBluesealProductIncomplete',['id','productVariantId'],$_GET);
        if(!empty($this->authorizedShops)){
            $datatable->addCondition('shopId',$this->authorizedShops);
        }

        $prodotti = $this->app->repoFactory->create('Product')->em()->findBySql($datatable->getQuery(),$datatable->getParams());
        $count = $this->em->products->findCountBySql($datatable->getQuery(true), $datatable->getParams());
        $totlalCount = $this->em->products->findCountBySql($datatable->getQuery('full'), $datatable->getParams());

        $modifica = $bluesealBase."prodotti/modifica";

        $response = [];
        $response ['draw'] = $_GET['draw'];
        $response ['recordsTotal'] = $totlalCount;
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
                $shops[] = $shop->name;
            }

            $tools = "";

            $tools .= $this->app->getUser()->hasPermission("/admin/product/list") ? '<span class="tools-spaced"><a href="'.$bluesealBase.'printAztecCode.php?src='.base64_encode($val->id.'-'.$val->productVariantId.'__'.$val->productBrand->name.' - '.$val->itemno.' - '.$val->productVariant->name).'" target="_blank"><i class="fa fa-barcode"></i></a></span>' : '<span class="tools-spaced"><i class="fa fa-barcode"></i></span>';
            $tools .= $this->app->getUser()->hasPermission('/admin/product/edit') ? '<span class="tools-spaced"><a href="'.$modifica.'?id='.$val->id.'&productVariantId='.$val->productVariantId.'"><i class="fa fa-pencil-square-o"></i></a></span>' : '<span class="tools-spaced"><i class="fa fa-pencil-square-o"></i></span>';

            $creationDate = new \DateTime($val->creationDate);
            $img = strpos($val->dummyPicture,'s3-eu-west-1.amazonaws.com') ? $val->dummyPicture : $dummyUrl."/".$val->dummyPicture;

            $response['aaData'][$i]["DT_RowClass"] = 'colore';
            $response['aaData'][$i]["id"] = $this->app->getUser()->hasPermission('/admin/product/edit') ? '<a href="'.$modifica.'?id='.$val->id.'&productVariantId='.$val->productVariantId.'">'.$val->id.'-'.$val->productVariantId.'</a>' : $val->id.'-'.$val->productVariantId;
            $response['aaData'][$i]["code"] = $val->itemno.' # '.$val->productVariant->name;
            $response['aaData'][$i]["shop"] = implode(',',$shops);
            $response['aaData'][$i]["season"] = !is_null($val->productSeason) ? $val->productSeason->name.' '.$val->productSeason->year : "Mancante";
            $response['aaData'][$i]["dummyPicture"] = isset($val->dummyPicture) && !empty($val->dummyPicture) ? '<img width="80" src="'.$img.'">' : "";
            $response['aaData'][$i]["brand"] = isset($val->productBrand) ? $val->productBrand->name : "";
            $response['aaData'][$i]["status"] = $val->productStatus->name;
            $response['aaData'][$i]["creationDate"] = $creationDate->format('d-m-Y H:i');
            $response['aaData'][$i]["problems"] = $this->parseProblem($val, $cats);

            $i++;
        }
        return json_encode($response);
    }

    /**
     * @param CProduct $product
     * @return string
     */
    private function parseProblem(CProduct $product, $cats = null)
    {
        $problems = [];

        if (is_null($product->productSizeGroup)) {
            $problems[] = "[E400] Gruppo taglie";
        }
        if (is_null($product->productSeason)) {
            $problems[] = "[E410] Stagione";
        }
        if (is_null($product->productBrand) || ($product->productBrand->id === 1)) {
            $problems[] = "[E420] Brand";
        }
        if (is_null($product->productSheetPrototype)) {
            $problems[] = "[E430] Scheda prodotto";
        }
        if (is_null($product->productColorGroup) || ($product->productColorGroup->isEmpty())) {
            $problems[] = "[E440] Gruppo colore";
        }
        if (is_null($cats) && empty($cats)) {
            $problems[] = "[E445] Nessuna categoria";
        }
        if (is_null($product->productPhoto) || ($product->productPhoto->isEmpty())) {
            $problems[] = "[E450] Foto";
        }
        if (is_null($product->productSku) || ($product->productSku->isEmpty())) {
            $problems[] = "[E460] Quantità non caricate";
        }
        if (is_null($product->productNameTranslation) || empty($product->productNameTranslation->getFirst()->name)) {
            $problems[] = "[E470] Nome Prodotto";
        }
	    if (is_null($product->productDescriptionTranslation) || empty($product->productDescriptionTranslation->getFirst()->description)) {
            $problems[] = "[E480] Descrizione Prodotto";
        }

        return '<ul style="padding:0;margin:0;list-style-type:decimal-leading-zero"><li>'.implode('</li><li>',$problems).'</ul>';
    }

    /*public function orderBy(){
        $dtOrderingColumns = $_GET['order'];
        $dbOrderingColumns = [
            ['column'=>'vProductShopView.itemno'],
            ['column'=>'vProductShopView.shopId'],
            ['column'=>'vProductShopView.externalId'],
            ['column'=>null],
            ['column'=>'vProductShopView.productBrandId'],
            ['column'=>null],
            ['column'=>'vProductShopView.status'],
            ['column'=>'vProductShopView.creationDate']
        ];
        $dbOrderingDefault = [
            ['column'=>'vProductShopView.creationDate','dir'=>'desc']
        ];

        $sqlOrder = " ORDER BY ";
        foreach ($dtOrderingColumns as $column) {
            if (isset($dbOrderingColumns[$column['column']]) && $dbOrderingColumns[$column['column']]['column'] !== null) {
                $sqlOrder .= $dbOrderingColumns[$column['column']]['column']." ".$column['dir'].", ";
            }
        }
        if (substr($sqlOrder,-1,2) != ', ') {
            foreach($dbOrderingDefault as $column) {
                $sqlOrder .= $column['column'].' '.$column['dir'].', ';
            }
        }
        return rtrim($sqlOrder,', ');
    }
*/
    public function post(){
        throw new \Exception();
    }
    
    public function delete(){
        throw new \Exception();
    }

}