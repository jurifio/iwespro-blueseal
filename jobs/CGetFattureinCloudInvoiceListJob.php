<?php

namespace bamboo\blueseal\jobs;
use bamboo\domain\entities\CDocument;
use bamboo\domain\entities\CUser;
use bamboo\domain\entities\CShop;


use bamboo\core\base\CFTPClient;
use bamboo\core\db\pandaorm\repositories\CRepo;
use bamboo\core\exceptions\BambooException;
use bamboo\core\exceptions\BambooFTPClientException;
use bamboo\domain\entities\CCartAbandonedEmailSend;
use bamboo\domain\repositories\CCartAbandonedEmailSendRepo;
use bamboo\domain\entities\COrder;
use bamboo\domain\entities\CCart;
use bamboo\domain\entities\CCartAbandonedEmailParam;
use bamboo\domain\entities\CCouponType;
use bamboo\domain\entities\CCoupon;
use bamboo\domain\entities\CCartLine;
use bamboo\core\base\CSerialNumber;
use bamboo\core\db\pandaorm\repositories\ARepo;
use bamboo\domain\repositories\CEmailRepo;
use bamboo\blueseal\remote\readextdbtable\CReadExtDbTable;

use bamboo\core\jobs\ACronJob;
use bamboo\domain\entities\CProductPublicSku;
use bamboo\domain\entities\CProduct;
use bamboo\core\events\AEventListener;


/**
 * Class CGetFattureinCloudInvoiceListJob
 * @package bamboo\blueseal\jobs
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 13/02/2019
 * @since 1.0
 */
class CGetFattureinCloudInvoiceListJob extends ACronJob
{
    /**
     * @param null $args
     * @throws \bamboo\core\exceptions\BambooDBALException
     */
    public function run($args = null)
    {
        $api_uid = $this->app->cfg()->fetch('fattureInCloud', 'api_uid');
        $api_uid ='34021';
        $api_key = $this->app->cfg()->fetch('fattureInCloud', 'api_key');
        $api_key ='443884d05056b5f0831446538c6e840f';
        $today = new \DateTime();
        $year = $today->format('Y');
        $start = $today->format('01/01/Y');
        $end =$today->format('d/m/Y');
        $requestJson='{
  "api_uid": "'.$api_uid.'",
  "api_key": "'.$api_key.'",
  "anno": "'.$year.'",
  "data_inizio": "'.$start.'",
  "data_fine": "'.$end.'",
  "cliente": "",
  "fornitore": "",
  "id_cliente": "",
  "id_fornitore": "",
  "saldato": "",
  "oggetto": "",
  "ogni_ddt": "",
  "PA": false,
  "PA_tipo_cliente": "",
  "pagina": 1
}';
        $urlInsert = "https://api.fattureincloud.it:443/v1/fatture/lista";
        $options = array(
            "http" => array(
                "header"  => "Content-type: text/json\r\n",
                "method"  => "POST",
                "content" => $requestJson
            ),
        );
        $context  = stream_context_create($options);
        $result = json_decode(file_get_contents($urlInsert, false, $context), true);
        $i=0;
        $res='';
        /** #var CRepo $userHasShopRepo */
        $userRepo=\Monkey::app()->repoFactory->create('UserHasShop');
        /** @var CDocument $documentRepo */
        $documentRepo=\Monkey::app()->repoFactory->create('Document');
        /** @var CRepo  $invoiceLineRepo */
        $invoiceLineRepo=\Monkey::app()->repoFactory->create('InvoiceLine');
        /** @var CRepo $invoiceBinRepo */
        $invoiceBinRepo=\Monkey::app()->repoFactory->create('InvoiceBin');
        $iresult=$result['numero_risultati'];
        if($iresult!=0) {
            for ($i=0;$i<$iresult;$i++){
                $invoiceNumber=$result['lista_documenti'][$i]['numero'];
                $invoiceDate=$result['lista_documenti'][$i]['data'];
                $idinvoiceFattureinCloud=$result['lista_documenti'][$i]['id'];
                $sectionalArr = explode(DIRECTORY_SEPARATOR, $invoiceNumber);
                $sectional = end($sectionalArr);
                $idClienteFattureinCloud=$result['lista_documenti'][$i]['id_cliente'];
                $paymentDate=$result['lista_documenti'][$i]['prossima_scadenza'];
                $paymentExpectedDate=$result['lista_documenti'][$i]['prossima_scadenza'];
                $link_doc=$result['lista_documenti'][$i]['link_doc'];
                $paydAmount=$result['lista_documenti'][$i]['importo_totale'];;
                $totalWithVat=$result['lista_documenti'][$i]['importo_totale'];
                $requestDetailInvoiceJson='{
                                             "api_uid": "'.$api_uid.'",
                                             "api_key": "'.$api_key.'",
                                             "id": "'.$idinvoiceFattureinCloud.'",
                                             "token": "1234567890abcdefghijklmnopqrstuv"
                                             }';
                $urlDetailInsert = "https://api.fattureincloud.it:443/v1/fatture/dettagli";
                $options = array(
                    "http" => array(
                        "header"  => "Content-type: text/json\r\n",
                        "method"  => "POST",
                        "content" => $requestDetailInvoiceJson
                    ),
                );
                $context  = stream_context_create($options);
                $resultDetail = json_decode(file_get_contents($urlDetailInsert, false, $context), true);
                $piva=$resultDetail['dettagli_documento']['piva'];

                $priceNoVat=$resultDetail['dettagli_documento']['importo_netto'];
                $totalWithVat=$resultDetail['dettagli_documento']['importo_totale'];
                $vat='22';
                $description='servizi vari';
                $sql="select ua.id as billinAddressId ,S2.userId as userId,RIGHT(ua.vatNumber,11)as vatNumber, S.id as shopId  from AddressBook ua
                      join Shop S on ua.id=S.billingAddressBookId
                      join UserHasShop S2 ON S.id = S2.shopId where  RIGHT(ua.vatNumber,11) ='".$piva."'";
                $findUser=\Monkey::app()->dbAdapter->query($sql,[])->fetchAll();
                foreach($findUser as $userIds){
                    $userId=$userIds['userId'];
                    $shopId=$userIds['shopId'];
                }

                $findIfInvoiceExist=$documentRepo->findOneBy(['number'=>$invoiceNumber,'year'=>$year]);
                if(is_null($findIfInvoiceExist)){
                    if($sectional=='W') {
                        $insertDocument = $documentRepo->getEmptyEntity();
                        $insertDocument->userId = $userId;
                        $insertDocument->shopRecipientId = '1';
                        $insertDocument->number = $invoiceNumber;
                        $invoiceDatefordbYear=substr($invoiceDate,6,4);
                        $invoiceDatefordbMonth=substr($invoiceDate,3,2);
                        $invoiceDatefordbDay=substr($invoiceDate,0,2);
                        $invoiceDateforDb = $invoiceDatefordbYear.'-'.$invoiceDatefordbMonth.'-'.$invoiceDatefordbDay.' 00:00:00';
                        $invoicepaymentDatefordbYear=substr($invoiceDate,6,4);
                        $invoicepaymentDatefordbMonth=substr($invoiceDate,3,2);
                        $invoicepaymentDatefordbDay=substr($invoiceDate,0,2);
                        $paymentDateforDb = $invoicepaymentDatefordbYear.'-'.$invoicepaymentDatefordbMonth.'-'.$invoicepaymentDatefordbDay.' 00:00:00';


                        $insertDocument->date = $invoiceDateforDb;
                        $insertDocument->paydAmount = $totalWithVat;
                        $insertDocument->paymentExpectedDate = $paymentDateforDb;
                        $insertDocument->paymentDate = $paymentDateforDb;
                        $insertDocument->invoiceTypeId = '7';
                        $insertDocument->totalWithVat = $totalWithVat;
                        $insertDocument->year = $year;
                        $insertDocument->insert();
                        $newInvoiceId = $documentRepo->findOneBy(['number'=>$invoiceNumber,'year'=>$year]);
                        $invoiceid=$newInvoiceId->id;
                        $insertInvoiceLine = \Monkey::app()->repoFactory->create('InvoiceLine')->getEmptyEntity();
                        $insertInvoiceLine->invoiceId = $invoiceid;
                        $insertInvoiceLine->description = $description;
                        $insertInvoiceLine->priceNoVat = $priceNoVat;
                        $insertInvoiceLine->vat = $vat;
                        $insertInvoiceLine->price = $totalWithVat;
                        $insertInvoiceLine->insert();

                        $insertInvoiceBin = $invoiceBinRepo->getEmptyEntity();
                        $filenameArr = explode(DIRECTORY_SEPARATOR, $link_doc);
                        $filename = end($filenameArr);
                        $insertInvoiceBin->invoiceId=$invoiceid;
                        $insertInvoiceBin->filename = $filename;
                        $insertInvoiceBin->bin = file_get_contents($link_doc);
                        $insertInvoiceBin->insert();
                        $noexist=' fattura N°'.$invoiceNumber .'/'.$year.' inserita';
                    } else {


                        $noexist = ' Fattura N°'.$invoiceNumber .'/'.$year.' non del friend';
                    }



                }else{
                    $noexist=' fattura N°'.$invoiceNumber .'/'.$year.' gia esistente';
                }











                $res.=$noexist;
            }
        }


        return $res;


    }


}