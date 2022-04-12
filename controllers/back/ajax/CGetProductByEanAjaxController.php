<?php
namespace bamboo\controllers\back\ajax;
use bamboo\domain\entities\CProduct;

/**
 * Class CGetProductByEanAjaxController
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 08/04/2022
 * @since 1.0
 */
class CGetProductByEanAjaxController extends AAjaxController
{
    public function get() {
        $search = $this->app->router->request()->getRequestData('search');
        $limit = $this->app->router->request()->getRequestData('limit');
        $wideSearch = $this->app->router->request()->getRequestData('wideSearch');
        $search = str_replace(' ','%',$search);
        $limit = ($limit) ? $limit : 1;

        $query = "SELECT myView.id as id, 
                         myView.productVariantId as productVariantId 
                  from (SELECT p.id, 
                               p.productVariantId, 
                               p.externalId,
                               concat_ws(',',
                                    concat(p.externalId),
                                    CONCAT(ds.barcode),
                                  concat(pb.name),
                                  concat(p.id,'-', p.productVariantId), 
                                  concat(p.itemno, '#', v.name), 
                                  concat(p.itemno, ' # ', v.name)) as ricerca
                          FROM Product p 
                            join ProductVariant v on p.productVariantId = v.id 
                            join ProductBrand pb on p.productBrandId = pb.id
                            join ProductStatus ps on p.productStatusId = ps.id left  JOIN DirtyProduct dp ON dp.productId=p.id AND dp.productVariantId=p.productVariantId
						    JOIN DirtySku ds ON dp.id=ds.dirtyProductId
                  
                            group by p.id, p.productVariantId) as myView
                  WHERE ricerca like '%".$search."%' limit 1";

        $products = \Monkey::app()->repoFactory->create('Product')->findBySql($query, []);
        $ret = [];
        foreach($products as $v) {
            /** @var CProduct $v */

            $row['id'] = $v->id;
            $row['productVariantId']=$v->productVariantId;
            $row['resultCPF'] = $v->printCpf();
            $row['resultBrand'] = $v->productBrand->name;
            $row['resultVariante'] = $v->productVariant->name;

            $ret[] = $row;
        }
        return json_encode($ret);
    }
}