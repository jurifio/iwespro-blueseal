<?php
namespace bamboo\controllers\back\ajax;

/**
 * Class CUserList
 * @package bamboo\blueseal\controllers\ajax
 *
 * @author Iwes  International Web Ecommerce ServicesTeam <juri@iwes.it>
 *
 * @copyright (c) Iwes International Web Ecommerce Services - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 22/07/2016
 * @since 1.0
 */
class CMagMovements extends AAjaxController
{
    public function get()
    {
        /** @var CUserRepo */
        $user = $this->app->getUser();
        $code = $this->app->router->request()->getRequestData('code');
        $shop = $this->app->router->request()->getRequestData('shop');
        $defaultCause = $this->app->router->request()->getRequestData('defaultCause');
        $ret = [];

        if ($user->hasPermission('/admin/friend/friendProductEdit')) {
            if ($code && $shop) {
                $productRepo = \Monkey::app()->repoFactory->create('Product');
                $product = $productRepo->findOneByStringId($code);
                $ret = $product->toArray();
                $ret['sku'] = [];
                foreach($product->productSku as $v) {
                    /** @var CProductSkuRepo */
                    $ret['sku'][] = $v->toArray();
                }
                $ret['sizeGroup'] = $v->productSizeGroup->toArray();
                $ret['sizes'] = [];
                foreach($product->productSize as $v) {
                    $ret['sizes'][$v->id] = $v;
                }
                return json_encode($ret);
            } elseif ($defaultCause) {
                $causes = \Monkey::app()->repoFactory->create('StorehouseOperationCause')->findAll();
                $ret = [];
                $i = 0;
                foreach($causes as $v) {
                    $ret[$i] = $v->toArray();
                    $ret[$i]['default'] = ($v->id = $defaultCause) ? 1 : 0;
                    $i++;
                }
                return json_encode($ret);
            }
        }
        return "Non hai i permessi per leggere i dati";
    }

    public function put() {
        //TODO: all
    }

    public function post() {
        //TODO: all
    }

}