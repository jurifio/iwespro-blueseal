<?php

namespace bamboo\controllers\back\ajax;

use bamboo\core\base\CObjectCollection;
use bamboo\core\db\pandaorm\repositories\CRepo;
use bamboo\core\exceptions\BambooException;
use bamboo\domain\entities\CGainPlanPassiveMovement;
use http\Env;
use PDO;
use PDOException;

/**
 * Class CInvoiceManage
 * @package bamboo\controllers\back\ajaxù
 *
 */
class CInvoiceManage extends AAjaxController
{
    /**
     * @return string
     */
    public function put()
    {

        $request = \Monkey::app()->router->request();
        $invoiceId = $request->getRequestData('invoiceId');
        $invoiceType = $request->getRequestData('invoiceType');
        $invoiceYear = $request->getRequestData('invoiceYear');
        $invoiceSiteChar = $request->getRequestData('invoiceSiteChar');
        $invoiceNumber = $request->getRequestData('invoiceNumber');
        $invoiceDate = $request->getRequestData('invoiceDate');
        $invoiceShopId = $request->getRequestData('invoiceShopId');
        $invoiceText = $request->getRequestData('invoiceText');
        $orderRepo = \Monkey::app()->repoFactory->create('Order');
        $shopRepo=\Monkey::app()->repoFactory->create('Shop');
        $res = '';
        try {
            $updateInvoice = \Monkey::app()->repoFactory->create('Invoice')->findOneBy(['id' => $invoiceId]);
            $orderId = $updateInvoice->orderId;
            $updateInvoice->invoiceYear = $invoiceYear;
            $updateInvoice->invoiceType = $invoiceType;
            $updateInvoice->invoiceSiteChar = $invoiceSiteChar;
            $updateInvoice->invoiceNumber = $invoiceNumber;
            $updateInvoice->invoiceDate = $invoiceDate;
            $updateInvoice->invoiceShopId = $invoiceShopId;
            $updateInvoice->invoiceText = $invoiceText;
            $updateInvoice->update();
            $res .= 'Fattura numero '.$invoiceType.'-'.$invoiceNumber.'/'.$invoiceYear.' in Iwes Modificata Correttamente<br>';
        } catch (\Throwable $e) {
            \Monkey::app()->applicationLog('CInvoiceManage','error','modify invoice',$e,'');
            $res .= 'Fattura numero'.$invoiceType.'-'.$invoiceNumber.'/'.$invoiceYear.' in  Iwes non Modificata<br>';
        }
        $order = $orderRepo->findOneBy(['id' => $orderId]);
        $remoteOrderSellerId = $order->remoteOrderSellerId;
        $remoteShopSellerId = $order->remoteShopSellerId;
        $findShopId = $shopRepo->findOneBy(['id' => $remoteShopSellerId]);
        if ($findShopId->hasEcommerce == '1' && $findShopId->id != '44') {
            /* find  orderId*/
            $db_host = $findShopId->dbHost;
            $db_name = $findShopId->dbName;
            $db_user = $findShopId->dbUsername;
            $db_pass = $findShopId->dbPassword;
            try {

                $db_con = new PDO("mysql:host={$db_host};dbname={$db_name}",$db_user,$db_pass);
                $db_con->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

            } catch (PDOException $e) {
                throw new BambooException('fail to connect');

            }
            if (ENV == 'prod') {
                try {
                    $stmtUpdateInvoice = $db_con->prepare('UPDATE Invoice set
                                                invoiceText=\'' . htmlentities($invoiceText) . '\' WHERE
                                                invoiceYear= \'' . $invoiceYear . '\' AND
                                                invoiceType=\'' . $invoiceType . '\' AND 
                                                invoiceSiteChar=\'' . $invoiceSiteChar . '\' AND
                                                invoiceNumber=\'' . $invoiceNumber . '\' AND
                                                invoiceDate=\'' . $invoiceDate . '\' AND 
                                                orderId =\'' . $remoteOrderSellerId . '\'
                                                 ');
                    $stmtUpdateInvoice->execute();
                    $res .= 'Fattura Remota numero '.$invoiceType.'-'.$invoiceNumber.'/'.$invoiceYear.' Modificata Correttamente<br>';
                } catch (\Throwable $e) {
                    \Monkey::app()->applicationLog('CInvoiceManage','error','modify Remote Seller invoice',$e,'');
                    $res .= 'Fattura Remota numero '.$invoiceType.'-'.$invoiceNumber.'/'.$invoiceYear.' non Modificata <br>';
                }
            }

        }

        return $res;


    }

    /**
     * @return string
     * @throws \bamboo\core\exceptions\BambooException
     * @throws \bamboo\core\exceptions\BambooORMInvalidEntityException
     * @throws \bamboo\core\exceptions\BambooORMReadOnlyException
     */
    public function post()
    {

    }

    /**
     * @return string
     * @throws \bamboo\core\exceptions\BambooException
     * @throws \bamboo\core\exceptions\BambooORMInvalidEntityException
     * @throws \bamboo\core\exceptions\BambooORMReadOnlyException
     */
    public function delete()
    {
        $request = \Monkey::app()->router->request();
        $invoiceId = $request->getRequestData('invoiceId');
        $orderRepo = \Monkey::app()->repoFactory->create('Order');
        $shopRepo=\Monkey::app()->repoFactory->create('Shop');
        $res='';

        try {
            $deleteInvoice = \Monkey::app()->repoFactory->create('Invoice')->findOneBy(['id' => $invoiceId]);
            $orderId=$deleteInvoice->orderId;
            $invoiceSiteChar=$deleteInvoice->invoiceSiteChar;
            $invoiceType=$deleteInvoice->invoiceType;
            $invoiceNumber=$deleteInvoice->invoiceNumber;
            $invoiceYear=$deleteInvoice->invoiceYear;
            $order = $orderRepo->findOneBy(['id' => $orderId]);
            $remoteOrderSellerId = $order->remoteOrderSellerId;
            $remoteShopSellerId = $order->remoteShopSellerId;

            $deleteInvoice->delete();
            $res .= 'Fattura numero '.$invoiceType.'-'.$invoiceNumber.'/'.$invoiceYear.' in Iwes Cancellata Correttamente<br>';
        } catch (\Throwable $e) {
            \Monkey::app()->applicationLog('CInvoiceManage','error','modify invoice',$e,'');
            $res .= 'Fattura numero'.$invoiceType.'-'.$invoiceNumber.'/'.$invoiceYear.' in  Iwes non Cancellata<br>';
        }
        $findShopsId = $shopRepo->findBy(['HasEcommerce' => 1]);
        foreach($findShopsId as $findShopId ) {
            if ($findShopId->id != '44') {
                /* find  orderId*/
                $db_host = $findShopId->dbHost;
                $db_name = $findShopId->dbName;
                $db_user = $findShopId->dbUsername;
                $db_pass = $findShopId->dbPassword;
                try {

                    $db_con = new PDO("mysql:host={$db_host};dbname={$db_name}",$db_user,$db_pass);
                    $db_con->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
                } catch (PDOException $e) {
                    throw new BambooException('fail to connect');

                }
                if (ENV == 'prod') {
                    try {
                        $stmtDeleteInvoice = $db_con->prepare('DELETE Invoice WHERE
                                                invoiceYear= \'' . $invoiceYear . '\' AND
                                                invoiceType=\'' . $invoiceType . '\' AND 
                                                invoiceSiteChar=\'' . $invoiceSiteChar . '\' AND
                                                invoiceNumber=\'' . $invoiceNumber . '\'
                                                 ');
                        $stmtDeleteInvoice->execute();
                        $res .= 'Fattura Remota numero ' . $invoiceType . '-' . $invoiceNumber . '/' . $invoiceYear . ' Cancellata Correttamente<br>';
                    } catch (\Throwable $e) {
                        \Monkey::app()->applicationLog('CInvoiceManage','error','modify Remote Seller invoice',$e,'');
                        $res .= 'Fattura Remota numero ' . $invoiceType . '-' . $invoiceNumber . '/' . $invoiceYear . ' non Cancellata <br>';
                    }
                }
            }
        }
        return $res;
    }
}