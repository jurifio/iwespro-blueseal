<?php

namespace bamboo\controllers\back\ajax;

use bamboo\core\base\CObjectCollection;
use bamboo\domain\entities\CFoison;
use bamboo\domain\entities\CProductBatch;
use bamboo\domain\entities\CProductBatchDetails;
use bamboo\domain\repositories\CEmailRepo;
use bamboo\domain\repositories\CProductBatchRepo;
use bamboo\utils\time\SDateToolbox;
use bamboo\utils\time\STimeToolbox;


/**
 * Class CUnfitProductBatchManage
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 01/06/2018
 * @since 1.0
 */
class CUnfitProductBatchManage extends AAjaxController
{
    /**
     * @return string
     * @throws \Exception
     * @throws \bamboo\core\exceptions\BambooException
     * @throws \bamboo\core\exceptions\BambooORMInvalidEntityException
     * @throws \bamboo\core\exceptions\BambooORMReadOnlyException
     */
    public function post()
    {

        $pb = \Monkey::app()->router->request()->getRequestData('pB');
        $dayPlus = \Monkey::app()->router->request()->getRequestData('dayPlus');

        /** @var CProductBatchRepo $pBatchRepo */
        $pBatchRepo = \Monkey::app()->repoFactory->create('ProductBatch');

        /** @var CEmailRepo $mailRepo */
        $mailRepo = \Monkey::app()->repoFactory->create('Email');

        $unfitProduct = '';
        /** @var CProductBatch $pBatch */
        $pBatch = $pBatchRepo->findOneBy(['id' => $pb]);

        $res = $pBatch->isValid();

        if ($res === 'ok') return "I componenti del lotto sembrano essere normalizzati correttamente";

        foreach ($res as $val) {
            $unfitProduct .= $val . '<br>';
        }


        if (is_null($pBatch->unfitDate)) {
            $pBatchRepo->qualityRank($pBatch);
        }


        $nowObject = new \DateTime();
        $now = $nowObject->format('Y-m-d H:i:s');

        if(!empty($dayPlus)) {
            if ($now <= $pBatch->scheduledDelivery) {
                $newDeliver = SDateToolbox::GetDateAfterAddedDays(STimeToolbox::GetDateTime($pBatch->scheduledDelivery), $dayPlus)->format('Y-m-d 23:59:59');
                $pBatch->scheduledDelivery = $newDeliver;
                $pBatch->tolleranceDelivery = SDateToolbox::GetDateAfterAddedDays(STimeToolbox::GetDateTime($newDeliver), 5)->format('Y-m-d 23:59:59');
            } elseif ($now > $pBatch->scheduledDelivery && $now <= $pBatch->tolleranceDelivery) {
                $pBatch->tolleranceDelivery = SDateToolbox::GetDateAfterAddedDays(STimeToolbox::GetDateTime($pBatch->tolleranceDelivery), $dayPlus)->format('Y-m-d 23:59:59');
            }
        }

        $pBatch->unfitDate = date('Y-m-d H:i:s');
        $pBatch->update();


        if ($pBatch->isUnassigned == 1) {
            $pBatchRepo->duplicateProductBatchFromCancelled($pBatch);

            /** @var CFoison $foison */
            $foison = $pBatch->contractDetails->contracts->foison;
            $foison->totalRank(true);

        }

        if (ENV == 'prod' && $pBatch->isUnassigned == 0) {
            $body = "I seguenti prodotti non sono idonei:<br>
                    $unfitProduct";
            $mailRepo->newMail('gianluca@iwes.it', [$pb['fason']], [], [], "Prodotti non idonei", $body);
        }


        return 'Notifiche inviate con successo';

    }

}