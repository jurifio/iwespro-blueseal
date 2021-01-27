<?php
namespace bamboo\controllers\back\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\core\intl\CLang;
use bamboo\domain\entities\CBillRegistryProduct;
use bamboo\domain\entities\CBillRegistryGroupProduct;
use bamboo\domain\entities\CBillRegistryCategoryProduct;
use bamboo\domain\entities\CPlanningWork;
use DateTime;

/**
 * Class CPlanningWorkListAjaxController
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 10/02/2020
 * @since 1.0
 */
class CPlanningWorkListAjaxController extends AAjaxController
{
    public function get()
    {
        $sql = 'SELECT
                      t1.id as id,
                       DATE_FORMAT(t1.startDateWork, "%d-%m-%Y %k:%i:%s") as startDateWork,
                       DATE_FORMAT(t1.endDateWork, "%d-%m-%Y %k:%i:%s") as endDateWork,
                      t1.billRegistryClientId as clientId,
       t1.shopId as ShopId,
       t1.request as title,
       t1.dateCreate as dateCreate,
       t1.dateUpdate as dateUpdate,
       t1.hour as hour,
       t1.cost as cost,
       pt.name as planningWorkType,
       if(t1.planningType=1,"da Ticket","da Modulo") as planningType,
       t1.percentageStatus as percentageStatus,
       format((t1.cost*t1.hour),2) as total,
       `st`.`name` as status,
       `s`.`name` as shopName,
       `brc`.`companyName` as `companyName`       
                    FROM `PlanningWork` t1 join Shop s on t1.shopId=s.id 
                    left join BillRegistryClient brc on t1.billRegistryClientId=brc.id
                    left join BillRegistryInvoice bri on t1.billRegistryInvoiceId=bri.id    
                    join PlanningWorkStatus st on t1.planningWorkStatusId=st.id
                    join PlanningWorkType pt on t1.planingWorkTypeId=pt.id order by t1.startDateWork DESC';
        $datatable = new CDataTables($sql, ['id'], $_GET, true);

        $datatable->doAllTheThings();

        $planningworkedit = $this->app->baseUrl(false) . "/blueseal/planning/modifica/";
        /** @var CRepo $planningWorkRepo */
        $planningWorkRepo = \Monkey::app()->repoFactory->create('PlanningWork');
        /** @var  CShop $shopRepo */
        $shopRepo=\Monkey::app()->repoFactory->create('Shop');
        /**  @var CRepo $billRegistryInvoiceRepo */
        $billRegistryInvoiceRepo=\Monkey::app()->repoFactory->create('BillRegistryInvoice');
        /**
         * @var  CBillRegistryClient $brcRepo;
         */
        $brcRepo=\Monkey::app()->repoFactory->create('BillRegistryClient');
        foreach ($datatable->getResponseSetData() as $key => $row) {
            /** @var CPlanningWork $planningWork */
            $planningWork = $planningWorkRepo->findOne([$row['id']]);
            $row = [];
            $row["DT_RowId"] =  $planningWork->printId();
            $row['id'] = '<a href="'.$planningworkedit.$planningWork->id.'">'.$planningWork->id.'</a>';
            $row['request'] = $planningWork->request;
            $row['title'] = $planningWork->title;
            $planningWorkStatus=\Monkey::app()->repoFactory->create('PlanningWorkStatus')->findOneBy(['id'=>$planningWork->planningWorkStatusId]);
            $row['status']=$planningWorkStatus->name;
            $row['planningType']=($planningWork->planningType==1) ? 'da Ticket' : 'da Modulo' ;
            $planningWorkType=\Monkey::app()->repoFactory->create('PlanningWorkType')->findOneBy(['id'=>$planningWork->planningWorkTypeId]);
            $row['planningWorkType']=$planningWorkType->name;
            $row['startDateWork'] =(new \DateTime($planningWork->startDateWork))->format('d-m-Y H:i:s');
            $row['endDateWork'] =(new \DateTime($planningWork->endDateWork))->format('d-m-Y H:i:s');
            $shop=$shopRepo->findOneBy(['id'=>$planningWork->shopId]);
            $row['shopName']=$shop->name;
            $brc=$brcRepo->findOneBy(['id'=>$planningWork->billRegistryClientId]);
            if($brc) {
                $client = $brc->companyName;
            }else{
                $client='Non Assegnato';
            }
            $invoice='';
            if($planningWork->billRegistryInvoiceId!=null || $planningWork->billRegistryInvoiceId!=null) {
                $bri = $billRegistryInvoiceRepo->findOneBy(['id' => $planningWork->billRegistryInvoiceId]);
                if($bri){
                    $invoice=$bri->invoiceNumber.$bri->invoiceType.'/'.$bri->invoiceYear;
                }
            }
            $row['invoice']=$invoice;
            $row['companyName'] = $client;
            $row['cost'] = (!is_null($planningWork->cost)) ? number_format($planningWork->cost,2,',', '.'):'0';
            $row['hour'] = (!is_null($planningWork->hour))?  number_format($planningWork->hour,2,',', '.'):'0';
            $row['total'] =(!is_null($planningWork->hour))?  number_format(($planningWork->cost*$planningWork->hour),2,',','.'):'0';
            $row['percentageStatus']=$planningWork->percentageStatus.'%';
            $datatable->setResponseDataSetRow($key, $row);
        }

        return $datatable->responseOut();
    }
}