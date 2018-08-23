<?php

namespace bamboo\controllers\back\ajax;
use bamboo\domain\entities\CContractDetails;
use bamboo\domain\entities\CProductBatch;
use bamboo\domain\entities\CUser;
use bamboo\domain\repositories\CEmailRepo;


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

        $cDId = $pb->getContractDetailFromUnassignedProductBatch($user)->id;
        $pb->contractDetailsId = $cDId;
        $pb->marketplace = 0;
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