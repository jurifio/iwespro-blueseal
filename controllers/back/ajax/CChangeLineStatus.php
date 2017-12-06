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
        $dba = \Monkey::app()->dbAdapter;
        try {
            \Monkey::app()->repoFactory->beginTransaction();
            $ids = explode('-', $this->data['value']);
            /** @var COrderLineRepo $repo */
            $repo = \Monkey::app()->repoFactory->create('OrderLine');
            $line = $repo->findOne(['id' => $ids[0], 'orderId' => $ids[1]]);
            $oldActive = $line->orderLineStatus->isActive;

            $line = $repo->updateStatus($line, $ids[2]);
            $newActive = $line->orderLineStatus->isActive;
            \Monkey::app()->repoFactory->commit();
            if ($oldActive != $newActive) return 'reload';
            else return 'don\'t do it';
        } catch (BambooException $e) {
            \Monkey::app()->repoFactory->rollback();
            \Monkey::app()->router->response()->raiseProcessingError();
            return false;
        }
    }

}