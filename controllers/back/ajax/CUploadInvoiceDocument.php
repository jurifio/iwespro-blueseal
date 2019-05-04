<?php
namespace bamboo\controllers\back\ajax;

use bamboo\core\exceptions\BambooException;
use bamboo\domain\entities\CInvoiceDocument;
use bamboo\domain\entities\COrder;
use bamboo\domain\repositories\CInvoiceDocumentRepo;

/**
 * Class CUploadInvoiceDocument
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 02/05/2018
 * @since 1.0
 */
class CUploadInvoiceDocument extends AAjaxController
{
    /**
     * @return string
     */
    public function post()
    {
        $order = \Monkey::app()->router->request()->getRequestData('order');
        $type = \Monkey::app()->router->request()->getRequestData('type');

        if(empty($type) || !array_key_exists('file', $_FILES)){
            return json_encode('err');
        }


        /** @var CInvoiceDocumentRepo $invDocumentRepo */
        $invDocumentRepo = \Monkey::app()->repoFactory->create('InvoiceDocument');

        $insertFile =  $invDocumentRepo->insertNewInvoiceDocument($order, $_FILES['file'], $type);

        if($insertFile){
            $res = "ok";
        } else {
            $res = "err_grave";
        }

        return json_encode($res);
    }
}