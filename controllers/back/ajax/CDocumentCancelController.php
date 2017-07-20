<?php
namespace bamboo\controllers\back\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\core\intl\CLang;

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
    public function delete() {
        $documentId = \Monkey::app()->router->request()->getRequestData('documentId');
        $document = \Monkey::app()->repoFactory->create('Document')->findOne([$documentId]);
        $document->totalWithVat = 0;
        $document->note .= 'ANNULLATA';
        $document->update();

        return json_encode($documentId);
    }
}