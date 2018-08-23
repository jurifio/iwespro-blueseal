<?php

namespace bamboo\controllers\back\ajax;
use bamboo\domain\entities\CContractDetails;
use bamboo\domain\entities\CProductBatch;
use bamboo\domain\entities\CUser;
use bamboo\domain\repositories\CEmailRepo;
use bamboo\utils\time\SDateToolbox;


/**
 * Class CBookingWorkManage
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 23/08/2018
 * @since 1.0
 */
class CBookingWorkManage extends AAjaxController
{

    public function post()
    {
        /** @var CUser $user */
        $user = \Monkey::app()->getUser();
        //Se siamo noi non prenotiamo niente
        if($user->hasPermission('allShops')) return true;

        $pbId = \Monkey::app()->router->request()->getRequestData('pbId');

        /** @var CProductBatch $pb */
        $pb = \Monkey::app()->repoFactory->create('ProductBatch')->findOneBy(['id'=>$pbId]);


        $eWd = $pb->estimatedWorkDays;

        $cDId = $pb->getContractDetailFromUnassignedProductBatch($user)->id;
        $date = new \DateTime();
        $pb->contractDetailsId = $cDId;
        $pb->marketplace = 0;
        $pb->confirmationDate = date_format($date, 'Y-m-d H:i:s');
        $pb->scheduledDelivery = SDateToolbox::GetDateAfterAddedDays(null, $eWd)->format('Y-m-d 23:59:59');
        $pb->update();


        /** @var CContractDetails $contractDetails */
        $contractDetails = \Monkey::app()->repoFactory->create('ContractDetails')->findOneBY(['id'=>$cDId]);

        $sectionalCodeId = $contractDetails->workCategory->sectionalCodeId;

        $sectionalRepo = \Monkey::app()->repoFactory->create('Sectional');
        $pb->sectional = $sectionalRepo->createNewSectionalCode($sectionalCodeId);
        $pb->update();
        $foison = $user->foison;
        $foison->activeProductBatch = $pb->id;
        $foison->update();
        if(ENV == 'prod'){
            /** @var CEmailRepo $mail */
            $mail = \Monkey::app()->repoFactory->create('Email');
            $name = $user->foison->name. " ". $user->foison->surname;
            $body = "Il fason $name ha prenotato il lotto numero $pbId";
            $mail->newMail('gianluca@iwes.it', ["gianluca@iwes.it"], [], [], 'Prenotazione lotto', 'Il fason');
        }


        return $pbId;

    }
}