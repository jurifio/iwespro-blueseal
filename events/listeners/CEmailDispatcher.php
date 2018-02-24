<?php

namespace bamboo\events\listeners;

use bamboo\core\events\AEventListener;
use bamboo\core\events\CEventEmitted;
use bamboo\core\exceptions\BambooException;
use bamboo\export\order\COrderExport;

/**
 * Class CEmailDispatcher
 * @package bamboo\events\listeners
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 24/02/2018
 * @since 1.0
 */
class CEmailDispatcher extends AEventListener
{

    /**
     * @param $e
     * @return mixed|void
     * @throws BambooException
     */
    public function work($e)
    {
        try {
            $this->debug('DispatchOrderToFriendEvent', 'Starting', $e);
            if (!$e instanceof CEventEmitted) throw new BambooException('Event is not an event');
            \Monkey::app()->repoFactory->create('Email')->newMail(...$e->getEventData());
        } catch (\Throwable $e) {
            \Monkey::app()->applicationWarning(__CLASS__, 'Error while sending', $e->getMessage(),$e);
        }

    }
}