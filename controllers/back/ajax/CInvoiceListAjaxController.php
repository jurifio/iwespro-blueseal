<?php
namespace bamboo\controllers\back\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\core\intl\CLang;
use bamboo\domain\entities\CGainPlan;
use bamboo\domain\entities\CGainPlanPassiveMovement;

/**
 * Class CInvoiceListAjaxController
 * @package bamboo\controllers\back\ajax
 */
class CInvoiceListAjaxController extends AAjaxController
{
    public function get()
    {

        $sql = 'SELECT   i.id as id,
                        i.orderId as orderId,
                        i.invoiceYear as invoiceYear,
                        i.invoiceType as invoiceType,
                        i.invoiceSiteChar as invoiceSiteChar,
                        i.invoiceNumber as invoiceNumber,
                        i.invoiceDate as invoiceDate,
                        i.fattureInCloudId as fattureInCloudId,
                        i.fattureInCloudToken as fattureInCloudToken,
                        i.printSummaryDate as printSummaryDate,
                        s.name as invoiceShopId
                        from Invoice i left join `Order` O on i.orderId = O.id
                        left Join Shop s on i.invoiceShopId=s.id
                        order by invoiceDate DESC 
            
        ';
        $datatable = new CDataTables($sql, ['id'], $_GET, true);

        $datatable->doAllTheThings('true');


        $invoices = \Monkey::app()->repoFactory->create('Invoice')->findBySql($datatable->getQuery(),$datatable->getParams());
        $count = \Monkey::app()->repoFactory->create('Invoice')->em()->findCountBySql($datatable->getQuery(true), $datatable->getParams());
        $totalCount = \Monkey::app()->repoFactory->create('Invoice')->em()->findCountBySql($datatable->getQuery('full'), $datatable->getParams());

        $orderRepo=\Monkey::app()->repoFactory->create('Order');
        $orderLineRepo=\Monkey::app()->repoFactory->create('OrderLine');
        $shopRepo=\Monkey::app()->repoFactory->create('Shop');


        foreach ($datatable->getResponseSetData() as $key => $row) {
            /** @var $val CInvoice */
            $val = \Monkey::app()->repoFactory->create('Invoice')->findOneBy($row);
                $row['DT_RowId'] = $val->printId();
                $row['id'] = '<a href="/blueseal/fatture/modifica/' . $val -> printId() . '">' . $val -> printId() . '</a>';
                $row['invoiceYear']=$val->invoiceYear;
                $row['invoiceType']=$val->invoiceType;
                $row['invoiceNumber']=$val->invoiceNumber;
                $row['invoiceDate']=$val->invoiceDate;

                $row['orderId'] = $val->orderId;
                $order=$orderRepo->findOneBy(['id'=>$val->orderId]);
                $customerName=json_decode($order->frozenBillingAddress,false);
                if($customerName!=null) {
                    if ($customerName->name != null) {
                        $name = $customerName->name;
                    } else {
                        $name = '';
                    }
                    if ($customerName->surname != null) {
                        $surname = $customerName->surname;
                    } else {
                        $surname = '';
                    }
                    if ($customerName->company != null) {
                        $company = $customerName->company;
                    } else {
                        $company = '';
                    }
                    $row['customerName']=$name.' '.$surname.'<br>'.$company;
                }else{
                    $row['customerName']= 'non stampabile';
                }

                $datatable->setResponseDataSetRow($key,$row);
            }

            return $datatable->responseOut();
        }
    }