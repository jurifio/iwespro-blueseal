<?php

namespace bamboo\controllers\back\ajax;

use bamboo\core\base\CObjectCollection;
use bamboo\core\exceptions\BambooException;
use bamboo\core\exceptions\BambooOrderLineException;
use bamboo\core\exceptions\BambooShipmentException;
use bamboo\domain\entities\COrderLine;
use bamboo\domain\repositories\COrderLineRepo;
use bamboo\domain\repositories\CShipmentRepo;
use bamboo\utils\time\STimeToolbox;
use DateTime;
use PDO;
use PDOException;

/**
 * Class CSelectPaymentBillAjaxController
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 31/01/2020
 * @since 1.0
 */
class CSelectPaymentBillAjaxController extends AAjaxController
{
    public function get()
    {
        $paymentBill=[];
       $shopRecipientId = $this -> app -> router -> request() -> getRequestData('shopRecipientId');
        $res = $this -> app -> dbAdapter -> query('SELECT pb.id as paymentBillId,pb.creationDate as creationDate,pb.amountPaid as amountPaid,  d.shopRecipientId as shopId, pb.amount as amount
from PaymentBill  pb join PaymentBillHasInvoiceNew pbhi on pb.id=pbhi.paymentBillId 
    join Document d on pbhi.invoiceNewId=d.id 
where d.shopRecipientId='.$shopRecipientId.' and pb.submissionDate is null group by pb.id'
        , []) -> fetchAll();

        foreach ($res as $result) {
          $date=  new \DateTime($result['creationDate']);
          $creationDate=$date->format('d-m-Y');
            $paymentBill[] = ['id' => $result['paymentBillId'],
                'amount' =>  $result['amount'],
                'creationDate' =>$creationDate,
                'amountPaid'=>$result['amountPaid'],
                'restPaid'=>$result['amount']-$result['amountPaid']
                ];
        }

        return json_encode($paymentBill);
    }
}