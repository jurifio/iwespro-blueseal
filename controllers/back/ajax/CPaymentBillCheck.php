<?php
namespace bamboo\controllers\back\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\core\application\AApplication;
use bamboo\core\base\CObjectCollection;
use bamboo\core\exceptions\BambooInvoiceException;
use bamboo\core\intl\CLang;
use bamboo\domain\entities\CDocument;
use bamboo\domain\entities\CPaymentBill;
use bamboo\domain\entities\CProduct;
use bamboo\domain\repositories\CPaymentBillRepo;
use bamboo\utils\time\STimeToolbox;

/**
 * Class CPaymentBillListAjaxController
 * @package bamboo\blueseal\controllers\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date $date
 * @since 1.0
 */
class CPaymentBillCheck extends AAjaxController
{
    /**
     * @return string
     */
    public function get() {
        /** @var CPaymentBillRepo $paymentBillRepo */
        $paymentBillRepo = \Monkey::app()->repoFactory->create('PaymentBill');

        /** @var CObjectCollection $allPaymentBill */
        $allPaymentBill = $paymentBillRepo->findAll();

        $incongruentBill = [];
        $somma = [];

        /** @var CPaymentBill $pb */
        foreach ($allPaymentBill as $pb){

            /** @var CObjectCollection $allDocuments */
            $allDocuments = $pb->document;

            $sum = 0;
            /** @var CDocument $document */
            foreach ($allDocuments as $document){
                $sum += $document->getSignedValueWithVat();
            }

            if ((string)$pb->amount != (string)$sum ){
                $incongruentBill[] = [$pb->id, $sum];
            }

        }

        $res[] = $incongruentBill;

        return json_encode($res);

    }


    /**
     * @throws \bamboo\core\exceptions\BambooException
     * @throws \bamboo\core\exceptions\BambooORMInvalidEntityException
     * @throws \bamboo\core\exceptions\BambooORMReadOnlyException
     */
    public function put(){

        $idsBilling = \Monkey::app()->router->request()->getRequestData('checked');

        foreach ($idsBilling as $bill){

            /** @var CPaymentBill $billing */
            $billing = \Monkey::app()->repoFactory->create('PaymentBill')->findOneBy(['id'=> $bill[0]]);

            $billing->amount = $bill[1];
            $billing->update();
        }

        $res = "Distinte aggiornate correttamente";
        return $res;


    }

}