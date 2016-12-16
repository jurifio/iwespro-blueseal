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
class CFixProductGroupSizeAjaxController extends AAjaxController
{protected $urls = [];
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
        $query =
            "SELECT DISTINCT 
p.id as id, p.productVariantId as productVariantId,
creationDate,
group_concat(`psi`.`name` order by `psi`.`name` ASC separator '-') AS `problems` 
FROM Product as p
  JOIN ProductSku as ps On p.id = ps.productId AND p.productVariantId = ps.productVariantId
  join Shop ON ps.shopId = Shop.id
  JOIN ProductSize as psi ON ps.productSizeId = psi.id
where p.productSizeGroupId NOT IN (SELECT productSizeGroupId FROM ProductSizeGroupHasProductSize WHERE ProductSizeGroupHasProductSize.productSizeId = ps.productSizeId)
      AND p.productStatusId in (5,6,11) group by p.productVariantId";

        $datatable = new CDataTables($query,['id','productVariantId'],$_GET, true);
        $getQuery = $datatable->getQuery();
        $getParams = $datatable->getParams();
        $prodotti = $this->app->repoFactory->create('Product')->em()->findBySql($getQuery, $getParams);
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