<?php


namespace bamboo\controllers\back\ajax;


use bamboo\core\exceptions\BambooException;
use bamboo\core\exceptions\BambooStorehouseOperationException;

/**
 * Class CShopVisibleInvisible
 * @package bamboo\controllers\back\ajax
 * @author Iwes Team <it@iwes.it>, 30/08/2019
 * @copyright (c) Iwes snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @since 1.0
 */
class CShopVisibleInvisible extends AAjaxController
{
    /**
     * @return string
     */
    public function get() {
        try {
            $shp = \Monkey::app()->repoFactory->create('Shop')->findAll();
            $res = [];
            foreach ($shp as $v) {
                $single = [];
                $single['id'] = $v->id;
                $single['isVisible'] = $v->isVisible;
                $single['title'] = $v->title;
                $res[] = $single;
            }
            return json_encode($res);
        } catch (BambooException $e) {
            \Monkey::app()->router->response()->raiseProcessingError();
            return $e->getMessage();
        }
    }
    /**
     * @transaction
     */
    public function post()
    {

        $shopId = \Monkey::app()->router->request()->getRequestData('shopId');
        $action = \Monkey::app()->router->request()->getRequestData('action');

        $shop = \Monkey::app()->repoFactory->create('Shop')->findOneBy(['id'=>$shopId]);

        try {
            if ('stop' == $action) {
                $shop->isVisible=0;
                $shop->update();
                 $message = 'Friend Reso Invisibile!';
            } else{
                $shop->isVisible=1;
                $shop->update();
                $message = 'Friend Reso Visibile!';
            }

            return $message;

        } catch(BambooException $e) {
            \Monkey::app()->repoFactory->rollback();
            throw $e;
        }
    }
}