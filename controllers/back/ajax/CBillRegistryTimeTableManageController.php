<?php

namespace bamboo\controllers\back\ajax;

use bamboo\core\exceptions\BambooException;
use bamboo\core\exceptions\BambooInvoiceException;
use bamboo\core\exceptions\BambooOrderLineException;
use bamboo\core\exceptions\BambooShipmentException;
use bamboo\domain\entities\COrderLine;
use bamboo\domain\entities\CShipment;
use bamboo\domain\entities\CShipmentFault;
use bamboo\domain\repositories\COrderLineRepo;
use bamboo\domain\repositories\CShipmentRepo;
use bamboo\utils\time\SDateToolbox;
use bamboo\utils\time\STimeToolbox;

/**
 * Class CBillRegistryTimeTableManageController
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
class CBillRegistryTimeTableManageController extends AAjaxController
{
    /**
     * @return string
     */
    public function post()
    {
        $data = $this->app->router->request()->getRequestData();
        $billRegistryTimeTableId = $data['billRegistryTimeTableId'];
        $amountPayment=$data['amountPayment'];
        $amountPayment=str_replace(' €','',$amountPayment);
        $date=$data['datePayment'];
        $dateP=new \DateTime($date);
        $datePayment = $dateP->format('Y-m-d H:i:s');
        $billRegistryTimeTable = \Monkey::app()->repoFactory->create('BillRegistryTimeTable')->findOneBy(['id'=>$billRegistryTimeTableId]);
        $billRegistryTimeTable->amountPaid=$amountPayment;
        $billRegistryTimeTable->datePayment=$datePayment;
        $billRegistryTimeTable->update();
        $billRegistryInvoiceId=$billRegistryTimeTable->billRegistryInvoiceId;
        $findBillRegistryInvoiceTimeTable=\Monkey::app()->repoFactory->create('BillRegistryTimeTable')->findBy(['billRegistryInvoiceId'=>$billRegistryInvoiceId]);
        $findBillRegistryInvoice=\Monkey::app()->repoFactory->create('BillRegistryInvoice')->findOneBy(['id'=>$billRegistryInvoiceId]);
        $grossTotal=$findBillRegistryInvoice->grossTotal;
        foreach($findBillRegistryInvoiceTimeTable as $payment){
            if($payment->amountPaid!=null){
                $grossTotal-=$amountPayment;
            }

        }
        if($grossTotal<=0.1){
            $findBillRegistryInvoice->isPaid=1;
            $findBillRegistryInvoice->update();
        }



        return 'Pagamento Registrato con successo';
    }

    /**
     * @return string
     */
    public function put()
    {

    }

    public function get()
    {

    }

    /**
     * @transaction
     */
    public function delete()
    {

    }
}