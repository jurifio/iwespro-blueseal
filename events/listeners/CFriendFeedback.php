<?php

namespace bamboo\events\listeners;

use bamboo\core\events\AEventListener;
use bamboo\core\events\CEventEmitted;
use bamboo\core\exceptions\BambooException;
use bamboo\core\db\pandaorm\entities\AEntity;
use bamboo\domain\entities\CUser;

/**
 * Class COtherTest
 * @package bamboo\app\evtlisteners
 */
class CFriendFeedback extends AEventListener
{
    /**
     * @var CUser
     */
    protected $user;

    protected $backtrace = null;
    protected $params = null;

    /**
     * @param $e
     * @throws BambooException
     */
    public function work($e)
    {
        if(!$e instanceof CEventEmitted) throw new BambooException('Event is not an event');
        $status = $e->getEventData('status');
        $oldStatus = $e->getEventData('oldStatus');
        if ($status !== $oldStatus && ('ORD_FRND_OK' == $status || 'ORD_FRND_CANCEL' == $status)) {
            $this->backtrace = $e->getBacktrace();
            $orderLine = $e->getEventData('order');
            $soR = \Monkey::app()->repoFactory->create('StorehouseOperation');
            if ($status !== $oldStatus) {

                $dba = \Monkey::app()->dbAdapter;
                try {
                    \Monkey::app()->repoFactory->beginTransaction();
                    if ('ORD_FRND_OK' == $status) {
                        $accept = true;
                    } elseif ('ORD_FRND_CANC' == $status) {
                        $accept = false;
                    }
                    $soR->registerEcommerceSale($orderLine->shopId, [$orderLine->productSku], null, $accept);
                    $this->friendAcceptance($orderLine, $accept);
                    \Monkey::app()->repoFactory->commit();
                } catch (BambooException $e) {
                    \Monkey::app()->repoFactory->rollback();
                    \Monkey::app()->applicationError($e->getEventName(), 'FriendConfirmation', $e->getMessage());
                }
            }
        }
    }

}