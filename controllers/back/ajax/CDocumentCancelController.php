<?php
namespace bamboo\controllers\back\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\core\base\CObjectCollection;
use bamboo\core\intl\CLang;
use bamboo\domain\entities\CDocument;
use bamboo\domain\entities\CPaymentBill;

/**
 * Class CDocumentCancelController
 * @package bamboo\controllers\back\ajax
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
class CDocumentCancelController extends AAjaxController
{
    /**
     * @return string
     * @throws \bamboo\core\exceptions\BambooException
     * @throws \bamboo\core\exceptions\BambooORMInvalidEntityException
     * @throws \bamboo\core\exceptions\BambooORMReadOnlyException
     */
    public function delete() {
        $documentId = \Monkey::app()->router->request()->getRequestData('documentId');

        /** @var CDocument $document */
        $document = \Monkey::app()->repoFactory->create('Document')->findOne([$documentId]);
        //test
        /** @var CObjectCollection $paymentBill */
        $paymentBill = $document->paymentBill;

        /** @var CPaymentBill $singlePaymentBill */
        foreach ($paymentBill as $singlePaymentBill){
            $singlePaymentBill->amount -= $document->getSignedValueWithVat();
            $singlePaymentBill->update();
        }


        $document->totalWithVat = 0;
        $document->note .= 'ANNULLATA';
        $document->update();

        return json_encode($documentId);
    }
}