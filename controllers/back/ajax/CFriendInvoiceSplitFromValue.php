<?php
namespace bamboo\controllers\back\ajax;

use bamboo\domain\entities\CDocument;
use bamboo\domain\repositories\CDocumentRepo;
use bamboo\utils\price\SPriceToolbox;

/**
 * Class CFriendInvoiceSplitFromValue
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 15/01/2019
 * @since 1.0
 */
class CFriendInvoiceSplitFromValue extends AAjaxController
{
    public function put() {
        $data = $this->app->router->request()->getRequestData();
        if($data['parts'] < 0) return;

        /** @var CDocumentRepo $documentRepo */
        $documentRepo = \Monkey::app()->repoFactory->create('Document');

        \Monkey::app()->repoFactory->beginTransaction();

        foreach($data['invoicesId'] as $documentId) {
            /** @var CDocument $document */
            $document = $documentRepo->findOneByStringId($documentId);

            if(!$document->paymentBill->isEmpty()) {
                \Monkey::app()->repoFactory->rollback();
                throw new \Exception('Non puoi dividere una fattura già in distinta');
            }

            $newPrice = $document->totalWithVat - $data['parts'];

            $document2 = clone $document;
            unset($document2->id);
            $document2->number .= " ACC.";
            $document2->totalWithVat = $newPrice;
            $document2->insert();


            $document->number .= " SLD";
            $document->totalWithVat = $data['parts'];
            $document->update();

        }

        \Monkey::app()->repoFactory->commit();
    }
}