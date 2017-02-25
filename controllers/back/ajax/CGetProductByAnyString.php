<?php
namespace bamboo\controllers\back\ajax;
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
class CGetProductByAnyString extends AAjaxController
{
    public function get() {
        $search = $this->app->router->request()->getRequestData('search');
        $limit = $this->app->router->request()->getRequestData('limit');
        $onlyPublic = $this->app->router->request()->getRequestData('onlyPublic');
        $wideSearch = $this->app->router->request()->getRequestData('wideSearch');

        $limit = ($limit) ? $limit : 10;

        $query = "SELECT myView.id as id, 
                         myView.productVariantId as productVariantId 
                  from (SELECT p.id, 
                               p.productVariantId, 
                               concat_ws(',',
                                  concat(pb.name),
                                  concat(p.id,'-', p.productVariantId), 
                                  concat(p.itemno, '#', v.name), 
                                  concat(p.itemno, ' # ', v.name)) as ricerca
                          FROM Product p 
                            join ProductVariant v on p.productVariantId = v.id 
                            join ProductBrand pb on p.productBrandId = pb.id
                            join ProductStatus ps on p.productStatusId = ps.id
                            where ps.isVisible = ifnull(?, ps.isVisible)
                            group by p.id, p.productVariantId) as myView
                  WHERE ricerca like ? limit ?";
        $params = [];

        if($onlyPublic) $params[] = 1;
        else $params[] = null;

        if($wideSearch) {
            $search = str_replace(' ','%',$search);
        }
        $params[] = '%'.$search.'%';
        $params[] = $limit;

        $products = $this->app->repoFactory->create('Product')->findBySql($query, $params);
        $ret = [];
        foreach($products as $v) {
            /** @var CProduct $v */
            $row = $v->fullTreeToArray();;
            $row['code'] = $v->printId();
            $row['cpfVar'] = $v->printCpf();
            $row['brand'] = $v->productBrand->name;
            $row['publicUrl'] = $v->getProductUrl();
            $row['dummyUrl'] = $v->getDummyPictureUrl();
            $availableSizes = [];
            foreach($v->productSku as $sku) {
                if($sku->stockQty > 0) {
                    $availableSizes[$sku->productSizeId] = $sku->productSize->name;
                }
            }
            $row['availableSizes'] = array_values($availableSizes);
            $ret[] = $row;
        }
        return json_encode($ret);
    }
}