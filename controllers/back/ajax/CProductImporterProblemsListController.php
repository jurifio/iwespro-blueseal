<?php
namespace bamboo\controllers\back\ajax;

use bamboo\domain\entities\CProduct;
use bamboo\blueseal\business\CDataTables;
use bamboo\core\intl\CLang;
use bamboo\core\db\pandaorm\entities\CEntityManager;
use bamboo\core\db\pandaorm\adapter\CMySQLAdapter;

/**
 * Class CTestAjax.php
 * @package bamboo\app\controllers
 */
class CProductImporterProblemsListController extends AAjaxController
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
        $shops = [];


        $query =
"select 
`p`.`id` AS `id`,`p`.`productVariantId` AS `productVariantId`,
concat(`p`.`id`,'-',`p`.`productVariantId`) AS `productCode`,
concat(`p`.`itemno`,' # ',`pv`.`name`) AS `code`,
`s`.`name` AS `shop`,
`s`.`id` AS `shopId`,
`pb`.`name` AS `brand`,
`p`.`externalId` AS `externalId`,
`ps`.`name` AS `status`,concat_ws(' ',`psg`.`name`,`psg`.`macroName`,`psg`.`locale`) AS `sizeGroup`,
`p`.`creationDate` AS `creationDate`,
group_concat(`ds`.`size` order by `ds`.`size` ASC separator '-') AS `problems` 
from ((((((((`Product` `p` 
join `ProductVariant` `pv` on((`pv`.`id` = `p`.`productVariantId`))) 
join `ProductBrand` `pb` on((`p`.`productBrandId` = `pb`.`id`))) 
join `ProductStatus` `ps` on((`p`.`productStatusId` = `ps`.`id`))) 
join `ProductSizeGroup` `psg` on((`p`.`productSizeGroupId` = `psg`.`id`))) 
join `DirtyProduct` `dp` on(((`p`.`id` = `dp`.`productId`) and (`p`.`productVariantId` = `dp`.`productVariantId`)))) 
join `DirtySku` `ds` on((`dp`.`id` = `ds`.`dirtyProductId`))) 
left join `ShopHasProduct` `sp` on(((`dp`.`productId` = `sp`.`productId`) 
and (`dp`.`productVariantId` = `sp`.`productVariantId`) 
and (`dp`.`shopId` = `sp`.`shopId`)))) 
join `Shop` `s` on `sp`.`shopId` = `s`.`id`) 
where 
((`ps`.`id` not in (7,8,12,13)) 
and (`s`.`importer` is not null) 
and (`ds`.`status` <> 'ok')) 
group by `dp`.`productId`,`dp`.`productVariantId`,`dp`.`shopId` having (sum(`ds`.`qty`) > 0)";

        $datatable = new CDataTables($query,['id','productVariantId'],$_GET, true);
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

	        /** @var CProduct $val */

            $creationDate = new \DateTime($val->creationDate);

            $response['aaData'][$i]["DT_RowId"] = $val->printId();
            $response['aaData'][$i]["DT_RowClass"] = 'colore';
            $response['aaData'][$i]["productCode"] = $this->app->getUser()->hasPermission('/admin/product/edit') ? '<span class="tools-spaced"><a href="'.$modifica.'?id='.$val->id.'&productVariantId='.$val->productVariantId.'">'.$val->id.'-'.$val->productVariantId.'</a></span>' : $val->id.'-'.$val->productVariantId;
            $response['aaData'][$i]["shop"] = implode(',',$shops);
            $response['aaData'][$i]["code"] = $val->itemno.' # '.$val->productVariant->name;
            $macroname = explode("_", explode("-", $val->productSizeGroup->macroName)[0])[0];
            $response['aaData'][$i]["sizeGroup"] = '<span class="small">' . $val->productSizeGroup->locale . '-' . $macroname . '</span>';
            $response['aaData'][$i]["dummyPicture"] = '<img width="80" src="'.$val->getDummyPictureUrl().'">';
            $response['aaData'][$i]["brand"] = isset($val->productBrand) ? $val->productBrand->name : "";
            //$response['aaData'][$i][$k++] = implode(',<br>',$cats);
            $response['aaData'][$i]["status"] = $val->productStatus->name;
            $response['aaData'][$i]["creationDate"] = $creationDate->format('d-m-Y H:i');
            $response['aaData'][$i]["problems"] = $this->parseProblem($val);

            $i++;
        }
        return json_encode($response);
    }

    /**
     * @param CProduct $product
     * @return string
     */
    private function parseProblem(CProduct $product)
    {
        $message = "[500] Size Mismatch";
        $sizes = $this->app->dbAdapter->query('SELECT size FROM DirtyProduct dp, DirtySku ds where dp.id = ds.dirtyProductId and dp.productId = ? and dp.productVariantId = ?',[$product->id,$product->productVariantId])->fetchAll();
        $newSize = [];
        foreach($sizes as $size){
            $newSize[] = $size['size'];
        }
        $message .= " ".implode('-',$newSize);
        return '<span>'.$message.'</span>';
    }
    
    public function post(){
        throw new \Exception();
    }
    
    public function delete(){
        throw new \Exception();
    }
}