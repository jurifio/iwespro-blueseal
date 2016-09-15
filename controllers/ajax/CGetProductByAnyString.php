<?php
namespace bamboo\blueseal\controllers\ajax;

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
        $limit = ($limit) ? $limit : 10;
        $ret = [];
        $i = 0;

        $query = "SELECT myView.id as id, myView.productVariantId as productVariantId from " .
            "(SELECT p.id, p.productVariantId, concat_ws(',',concat(p.id,'-', p.productVariantId), concat(p.itemno, '#', v.name), concat(p.itemno, ' # ', v.name)) as ricerca " .
            "FROM Product p join  ProductVariant v on p.productVariantId = v.id group by p.id, p.productVariantId) as myView" .
            " WHERE ricerca like ? limit " . $limit;
        $products = $this->app->repoFactory->create('Product')->findByAnyString($search, $limit);
        foreach($products as $v) {
            $ret[$i] = $v->fullTreeToArray();
            $ret[$i]['code'] = $v->id . '-' . $v->productVariantId;
            $ret[$i]['cpfVar'] = $v->itemno . ' # ' . $v->productVariant->name;
            $i++;
        }
        return json_encode($ret);
    }
}