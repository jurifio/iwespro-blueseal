<?php
namespace bamboo\controllers\back\ajax;

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
class CGetProductCategory extends AAjaxController
{
    public function get()
    {
        $code = $this->app->router->request()->getRequestData('code');
        $format = $this->app->router->request()->getRequestData('format');
        $format = (!$format) ? 'json' : $format;

        $prodRepo = \Monkey::app()->repoFactory->create('Product');
        $prod = $prodRepo->findOneByStringId($code);

        $cats = [];
        foreach($prod->productCategory as $c) {
            $cats[] = $c->id;
        } ;

        $ret = 'formato non riconosciuto';
        if ( 'json' == strtolower($format)) $ret = json_encode($cats);
        elseif ('string' == strtolower($format)) $ret = implode(',', $cats);

        return $ret;

    }
}