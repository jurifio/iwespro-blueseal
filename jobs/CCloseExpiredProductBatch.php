<?php

namespace bamboo\blueseal\jobs;

use bamboo\core\base\CObjectCollection;
use bamboo\domain\entities\CCart;
use bamboo\domain\entities\COrder;
use bamboo\core\jobs\ACronJob;
use bamboo\domain\entities\CProductBatch;
use bamboo\domain\repositories\CEmailRepo;
use bamboo\domain\repositories\CProductBatchRepo;
use bamboo\utils\time\SDateToolbox;
use bamboo\utils\time\STimeToolbox;

/**
 * Class CCloseExpiredProductBatch
 * @package bamboo\blueseal\jobs
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 24/08/2018
 * @since 1.0
 */
class CCloseExpiredProductBatch extends ACronJob
{
    /**
     * @param null $args
     */
    public function run($args = null)
    {
        $this->report('Close Product Batch', "Starting Closing");
        $this->closeExpiredPB();
    }

    /**
     *
     */
    public function closeExpiredPB()
    {
        $nowObject = new \DateTime();
        $now = $nowObject->format('Y-m-d H:i:s');


        /** @var CProductBatchRepo $pbRepo */
        $pbRepo = \Monkey::app()->repoFactory->create('ProductBatch');

        /** @var CEmailRepo $mailR */
        $mailR = \Monkey::app()->repoFactory->create('Email');


        /** @var CObjectCollection $pB */
        $pB = $pbRepo->findBy(['isUnassigned' => 0]);

        /** @var CProductBatch $productBatch */
        foreach ($pB as $productBatch) {
            $tolleranceClosing = SDateToolbox::GetDateAfterAddedDays(STimeToolbox::GetDateTime($productBatch->scheduledDelivery), 5)->format('Y-m-d 23:59:59');
            if ($now > $tolleranceClosing) {
                $productBatch->timingRank = 0;
                $productBatch->isUnassigned = 1;
                $productBatch->update();

                if (ENV == 'prod') {
                    $pBid = $productBatch->id;
                    $sub = "Il Lotto n. $pBid è scaduto";
                    $text = "
                Salve,<br>
                con la presente La informiamo che è scaduto il lotto n $pBid.
                Cordiali saluti,
                Iwes                
                ";
                    $mailR->newMail('operator@iwes.it', [$productBatch->contractDetails->contracts->foison->email], [], ['gianluca@iwes.it'], $sub, $text);
                }
            }


        }
    }
}