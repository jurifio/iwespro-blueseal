<?php
namespace bamboo\controllers\back\ajax;

use bamboo\domain\entities\CDocument;
use bamboo\domain\repositories\CDocumentRepo;
use bamboo\utils\price\SPriceToolbox;

/**
 * Class CFriendInoviceSplitter
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
class CFriendInvoiceSplitter extends AAjaxController
{
    public function put() {
        $data = $this->app->router->request()->getRequestData();
        if($data['parts'] < 2) return;

        /** @var CDocumentRepo $documentRepo */
        $documentRepo = $this->app->repoFactory->create('Document');

        $this->app->repoFactory->beginTransaction();

        foreach($data['invoicesId'] as $documentId) {
            /** @var CDocument $document */
            $document = $documentRepo->findOneByStringId($documentId);
            if(!is_null($document->paymentBill)) {
                $this->app->repoFactory->rollback();
                throw new \Exception('Non puoi dividere una fattura già in distinta');
            }
            $newPrice = SPriceToolbox::roundVat($document->totalWithVat / $data['parts']);

            for($i = 1; $i < $data['parts']; $i++) {
                $document2 = clone $document;
                unset($document2->id);
                $document2->number .= " ACC.".$i;
                $document2->totalWithVat = $newPrice;
                $document2->insert();

            }

            $document->number .= " SLD";
            $document->totalWithVat = $newPrice;
            $document->update();

        }

        $this->app->repoFactory->commit();
    }
}