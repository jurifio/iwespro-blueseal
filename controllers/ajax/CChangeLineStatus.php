<?php

namespace bamboo\blueseal\controllers\ajax;
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
            $dba->beginTransaction();
            $ids = explode('-', $this->data['value']);
            /** @var COrderLineRepo $repo */
            $repo = $this->app->repoFactory->create('OrderLine');
            $line = $repo->findOne(['id' => $ids[0], 'orderId' => $ids[1]]);
            $oldActive = $line->orderLineStatus->isActive;

            $line = $repo->updateStatus($line, $ids[2]);
            $newActive = $line->orderLineStatus->isActive;
            $dba->commit();
            if ($oldActive != $newActive) return 'reload';
            else return 'don\'t do it';
        } catch (BambooException $e) {
            $dba->rollback();
            \Monkey::app()->router->response()->raiseProcessingError();
            return false;
        }
    }

}