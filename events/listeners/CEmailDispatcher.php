<?php

namespace bamboo\events\listeners;

use bamboo\core\events\AEventListener;
use bamboo\core\events\CEventEmitted;
use bamboo\core\exceptions\BambooException;
use bamboo\domain\entities\CNewsletter;
use bamboo\domain\entities\CNewsletterShop;
use bamboo\domain\repositories\CEmailRepo;
use bamboo\domain\repositories\CExternalEmailRepo;
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
            //$this->report('DispatchOrderToFriendEvent', 'Starting', $e);
            if (!$e instanceof CEventEmitted) throw new BambooException('Event is not an event');
            $args = $e->getEventData();

            //vedo se è una newsletter esterna o interna
            $newsletterId = $args[7];
            /** @var CNewsletter $newsletter */
            $newsletter = \Monkey::app()->repoFactory->create('Newsletter')->findOneBy(['id' => $newsletterId]);
            $newsletterShopId = $newsletter->newsletterCampaign->newsletterShop->id;

            switch ($newsletterShopId) {
                case CNewsletterShop::PICKY:
                    /** @var CEmailRepo $emailRepo */
                    $emailRepo = \Monkey::app()->repoFactory->create('Email');
                    $emailRepo->newMail(...$args);
                    break;
                default:
                    /** @var CExternalEmailRepo $externalEmailRepo */
                    $externalEmailRepo = \Monkey::app()->repoFactory->create('ExternalEmail');
                    $externalEmailRepo->newExternalMail(...$args);
            }

        } catch (\Throwable $e) {
            $this->report('Error while sending', $e->getMessage(), $e);
        }

    }
}