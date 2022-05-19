<?php
namespace bamboo\controllers\back\ajax;

/**
 * Class CProductListAjaxController
 * @package bamboo\blueseal\controllers\ajax
 *
 * @author Iwes  International Web Ecommerce ServicesTeam <juri@iwes.it>, ${DATE}
 *
 * @copyright (c) Iwes International Web Ecommerce Services - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @since ${VERSION}
 */
class CGetProductCategoryPathList extends AAjaxController
{
    public function get()
    {
        $code = $this->app->router->request()->getRequestData('code');

        $productRepo = \Monkey::app()->repoFactory->create('Product');
        $product = $productRepo->findOneByStringId($code);
        if (!$product) return ['status' => 'ko', 'message' => 'Il prodotto cercato non è presente nel catalogo'];

        $cats = [];
        foreach($product->productCategory as $v) {
            $path = \Monkey::app()->categoryManager->categories()->getPath($v->id);
            unset($path[0]);
            $cats[] = '<span>'.implode('/',array_column($path, 'slug')).'</span>';
        }
        $ret = ['status' => 'ok', 'message' => 'Restituito l\'elenco delle categorie', 'cats' => $cats];
        return json_encode($ret);
    }
}