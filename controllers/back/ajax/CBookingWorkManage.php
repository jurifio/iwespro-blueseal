<?php

namespace bamboo\controllers\back\ajax;
use bamboo\domain\entities\CContractDetails;
use bamboo\domain\entities\CProductBatch;
use bamboo\domain\entities\CUser;
use bamboo\domain\repositories\CEmailRepo;
use bamboo\domain\repositories\CProductBatchDetailsRepo;
use bamboo\utils\time\SDateToolbox;
use bamboo\utils\time\STimeToolbox;


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

        try {

            \Monkey::app()->dbAdapter->beginTransaction();
            /** @var CUser $user */
            $user = \Monkey::app()->getUser();
            //Se siamo noi non prenotiamo niente
            if ($user->hasPermission('allShops')) return true;

            $pbId = \Monkey::app()->router->request()->getRequestData('pbId');

            /** @var CProductBatch $pb */
            $pb = \Monkey::app()->repoFactory->create('ProductBatch')->findOneBy(['id' => $pbId]);


            if($pb->workCategoryId == 1){

                $pDs = \Monkey::app()->dbAdapter->query(
                    'SELECT pbd.productId pbdId, pbd.productVariantId pbdV 
                  FROM ProductBatchDetails pbd
                  JOIN Product p ON p.id = pbd.productId AND pbd.productVariantId = p.productVariantId
                  WHERE p.productStatusId <> 6 AND pbd.productBatchId = ?', [$pb->id])->fetchAll();

                $prods = [];
                foreach ($pDs as $pD){
                    $prods[] = $pD['pbdId'] . '-' . $pD['pbdV'];
                }

                /** @var CProductBatchDetailsRepo $pbdRepo */
                $pbdRepo = \Monkey::app()->repoFactory->create('ProductBatchDetails');

                $pbdRepo->deleteProductFromBatch($pb->id, $prods, true);
            }


            $schedDelivery = SDateToolbox::GetDateAfterAddedDays(null, $pb->estimatedWorkDays)->format('Y-m-d 23:59:59');

            /** @var CContractDetails $cD */
            $cD = $pb->getContractDetailFromUnassignedProductBatch($user);
            $date = new \DateTime();
            $pb->contractDetailsId = $cD->id;
            $pb->marketplace = 0;
            $pb->confirmationDate = date_format($date, 'Y-m-d H:i:s');
            $pb->scheduledDelivery = $schedDelivery;
            $pb->tolleranceDelivery = SDateToolbox::GetDateAfterAddedDays(STimeToolbox::GetDateTime($schedDelivery), 5)->format('Y-m-d 23:59:59');
            $pb->isUnassigned = 0;
            $pb->update();

            $items = count($pb->getElements());
            $type = $cD->isVariable;

            if ($type == 0) {
                $newPrice = $pb->contractDetails->workPriceList->price * $items;
            } elseif ($type == 1) {
                $newPrice = $pb->unitPrice * $items;
            }
            $pb->value = $newPrice;
            $pb->update();

            /** @var CContractDetails $contractDetails */
            $contractDetails = \Monkey::app()->repoFactory->create('ContractDetails')->findOneBY(['id' => $cD->id]);

            $sectionalCodeId = $contractDetails->workCategory->sectionalCodeId;

            $sectionalRepo = \Monkey::app()->repoFactory->create('Sectional');
            $pb->sectional = $sectionalRepo->createNewSectionalCode($sectionalCodeId);
            $pb->update();
            $foison = $user->foison;
            $foison->activeProductBatch = $pb->id;
            $foison->update();
            if (ENV == 'prod') {
                /** @var CEmailRepo $mail */
                $mail = \Monkey::app()->repoFactory->create('Email');
                $name = $user->foison->name . " " . $user->foison->surname;
                $body = "Il fason $name ha prenotato il lotto numero $pbId";
                $mail->newMail('gianluca@iwes.it', ["gianluca@iwes.it"], [], [], 'Prenotazione lotto', $body);
            }

            \Monkey::app()->dbAdapter->commit();
        } catch (\Throwable $e){
            \Monkey::app()->dbAdapter->rollBack();
            \Monkey::app()->applicationError('ProductBatch', 'Accept product batch', $e->getMessage());
            return 'Errore durante la prenotazione del lotto';
        }


        return $pbId;

    }
}