<?php

namespace bamboo\controllers\back\ajax;
use bamboo\core\exceptions\BambooException;
use bamboo\domain\repositories\COrderLineRepo;

/**
 * Class CChangeLineStatus
 * @package bamboo\blueseal\controllers\ajax
 */
class CChangeLineStatus extends AAjaxController
{
    /**
     * @return bool
     */
    public function put()
    {
        try {
            $ids = explode('-', $this->data['value']);
            /** @var COrderLineRepo $repo */
            $repo = $this->app->repoFactory->create('OrderLine');
            $line = $repo->findOne(['id' => $ids[0], 'orderId' => $ids[1]]);
            $repo->updateStatus($line, $ids[2]);
            return true;
        } catch (BambooException $e) {
            \Monkey::app()->router->response()->raiseProcessingError();
            return false;
        }
    }

}