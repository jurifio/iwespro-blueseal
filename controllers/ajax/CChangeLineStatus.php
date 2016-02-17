<?php
/**
 * Created by PhpStorm.
 * User: Fabrizio Marconi
 * Date: 20/05/2015
 * Time: 13:00
 */
namespace bamboo\blueseal\controllers\ajax;
use bamboo\blueseal\business\COrderLineManager;

/**
 * Class CChangeLineShop
 * @package bamboo\app\controllers
 */
class CChangeLineStatus extends AAjaxController
{
    public function put()
    {
        $ids = explode('-',$this->data['value']);
        $repo = $this->app->repoFactory->create('OrderLine');
        $line = $repo->findOne(['id'=>$ids[0],'orderId'=>$ids[1]]);
        $om = new COrderLineManager($this->app,$line);
        $res = $om->changeStatus($ids[2]);
        if($res == false) {
            $this->app->router->response()->raiseProcessingError();
            return false;
        }
        return true;
    }

}