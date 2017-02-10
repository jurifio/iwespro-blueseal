<?php
namespace bamboo\blueseal\controllers\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\core\application\AApplication;
use bamboo\core\exceptions\BambooInvoiceException;
use bamboo\core\intl\CLang;
use bamboo\domain\entities\CPaymentBill;
use bamboo\domain\entities\CProduct;
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
class CPaymentBillManage extends AAjaxController
{
    /**
     * @return string
     */
    public function get() {
        $paymentBillId = $this->app->router->request()->getRequestData('paymentBillId');
        $paymentBill = $this->app->repoFactory->create('PaymentBill')->findOneBy($paymentBillId);
        return json_encode($paymentBill);
    }

    public function post()
    {
        $paymentBill = $this->app->repoFactory->create('PaymentBill')->getEmptyEntity();

        $paymentBill->paymentDate = STimeToolbox::DbFormattedDate($this->app->router->request()->getRequestData('paymentDate'));
        $paymentBill->amount = 0;
        $paymentBill->id = $paymentBill->insert();
        return json_encode($paymentBill);

    }

    public function put()
    {
        $paymentBillId = $this->app->router->request()->getRequestData('paymentBillId');
        /** @var CPaymentBill $paymentBill */
        $paymentBill = $this->app->repoFactory->create('PaymentBill')->findOneByStringId($paymentBillId);
        if($paymentBill->isSubmitted()) throw new BambooInvoiceException('Non puoi modificare una distinta già sottomessa');


        return true;
    }
}