<?php

namespace bamboo\controllers\back\ajax;

use bamboo\domain\entities\CUserAddress;
use bamboo\ecommerce\views\VBase;
use bamboo\blueseal\business\CBlueSealPage;
use bamboo\core\theming\CRestrictedAccessWidgetHelper;
use bamboo\domain\entities\CProductSku;
use bamboo\domain\entities\CUser;
use PDO;
use PDOException;

/**
 * Class CChangeOrderStatus
 * @package bamboo\blueseal\controllers\ajax
 *
 * @author Bambooshoot Team <emanuele@bambooshoot.agency>, ${DATE}
 *
 * @copyright (c) Bambooshoot snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @since ${VERSION}
 */
class CInvoiceAjaxController extends AAjaxController
{
    protected $fallBack = "blueseal";
    protected $pageSlug = "invoice_print";

    public function get()
    {
        $view = new VBase(array());
        $this->page = new CBlueSealPage($this->pageSlug, $this->app);
        $view->setTemplatePath($this->app->rootPath() . $this->app->cfg()->fetch('paths', 'blueseal') . '/template/invoice_print.php');

        $orderId = $this->app->router->request()->getRequestData('orderId');

        $orderRepo = \Monkey::app()->repoFactory->create('Order');
        $shopRepo = \Monkey::app()->repoFactory->create('Shop');
        $userAddressRepo =\Monkey::app()->repoFactory->create('UserAddress');
        $order = $orderRepo->findOneBy(['id' => $orderId]);

        $billingUserAddress = $order->billingAddressId;
        $orderUserAddress=$userAddressRepo->findOneBy(['id'=>$billingUserAddress]);

        $extraUe = $orderUserAddress->countryId;
        $countryRepo = \Monkey::app()->repoFactory->create('Country');
        $findIsExtraUe = $countryRepo->findOneBy(['id' => $extraUe]);
        $isExtraUe = $findIsExtraUe->extraue;

        if ($extraUe != '110') {
            $changelanguage = "1";

        } else {
            $changelanguage = "0";
        }

        // prendo l'intestazione
        $remoteShopSellerId = $order->remoteShopSellerId;
        $shopInvoices = $shopRepo->findOneBy(['id' => $remoteShopSellerId]);


        $logo = $shopInvoices->logo;
        $intestation = $shopInvoices->intestation;
        $intestation2 = $shopInvoices->intestation2;
        $address = $shopInvoices->address;
        $address2 = $shopInvoices->address2;
        $iva = $shopInvoices->iva;
        $tel = $shopInvoices->tel;
        $email = $shopInvoices->email;

        /***sezionali*/
        $receipt = $shopInvoices->receipt;
        $invoiceUe = $shopInvoices->invoiceUe;
        $invoiceExtraUe = $shopInvoices->invoiceExtraUe;
        $siteInvoiceChar = $shopInvoices->siteInvoiceChar;
        /*** dati db esterno ***/
        $db_host = $shopInvoices->dbHost;
        $db_name = $shopInvoices->dbName;
        $db_user = $shopInvoices->dbUsername;
        $db_pass = $shopInvoices->dbPassword;


        $hasInvoice = $order->hasInvoice;
        $invoiceRepo = \Monkey::app()->repoFactory->create('Invoice');
        $invoiceNew = $invoiceRepo->getEmptyEntity();
        $siteChar = $siteInvoiceChar;
        if ($order->invoice->isEmpty()) {
            try {
                $invoiceNew->orderId = $orderId;
                $today = new \DateTime();
                $invoiceNew->invoiceYear = $today->format('Y-m-d H:i:s');
                $year = (new \DateTime())->format('Y');
                $em = $this->app->entityManagerFactory->create('Invoice');
        // se è fattura
                if ($hasInvoice == '1') {
                    //se è extracee
                    if ($isExtraUe == '1') {
                        // se è Pickyshop
                        if ($remoteShopSellerId = 44) {
                            // è Pickyshop
                            $invoiceType = 'X';
                            $invoiceTypeVat = 'newX';
                            $documentType = '17';
                        } else {
                            //è Ecommerce Parallelo
                            $invoiceType = $invoiceExtraUe;
                            $invoiceTypeVat = 'newX';
                            $documentType = '20';
                        }
                        //se è non è inglese
                        if ($changelanguage != "1") {
                            // è inglese
                            $invoiceTypeText = "Fattura N. :";
                            $invoiceHeaderText = "FATTURA";
                            $invoiceTotalDocumentText = "Totale Fattura";
                            $documentType = '18';
                        } else {
                            //è italiano
                            $invoiceTypeText = "Invoice N. :";
                            $invoiceHeaderText = "INVOICE";
                            $invoiceTotalDocumentText = "Invoice Total";


                        }
                    } else {
                        // è fattura intracomunitario
                            // se è pickyshop
                        if ($remoteShopSellerId == '44') {
                            // è pickyshop
                            $invoiceType = 'P';
                            $invoiceTypeVat = 'newP';
                            $documentType = '17';
                        } else {
                            // è fattura Ecommerce Parallelo
                            $invoiceType = $invoiceUe;
                            $documentType = '21';
                            $invoiceTypeVat = 'newP';
                        }
                        // se non è inglese
                        if ($changelanguage != "1") {
                            // è italiano
                            $invoiceTypeText = "Fattura N. :";
                            $invoiceHeaderText = "FATTURA";
                            $invoiceTotalDocumentText = "Totale Fattura";
                        } else {
                            // non è italiano
                            $invoiceTypeText = "Invoice N. :";
                            $invoiceHeaderText = "INVOICE";
                            $invoiceTotalDocumentText = "Invoice Total";
                        }
                    }
                    // fine distinzione  tra intracee e extracee
                } else {
                    // è ricevuta
                    // se è pickyshop
                    if ($remoteShopSellerId == '44') {
                        //è pickyshop
                        $documentType = '16';
                        $invoiceType = 'K';
                        $invoiceTypeVat = 'newK';
                    } else {
                        // non è pickyshop
                        $invoiceType = $receipt;
                        $documentType = '22';
                        $invoiceTypeVat = 'newK';
                    }
                    // se non è inglese
                    if ($changelanguage != "1") {

                    // è italiano
                        $invoiceTypeText = "Ricevuta N. :";
                        $invoiceHeaderText = "RICEVUTA";
                        $invoiceTotalDocumentText = "Totale Ricevuta";
                    } else {
//è inglese
                        $invoiceTypeText = "Receipt N. :";
                        $invoiceHeaderText = "RECEIPT";
                        $invoiceTotalDocumentText = "Receipt Total";

                    }
                }


                $number = $em->query("SELECT ifnull(MAX(invoiceNumber),0)+1 AS new
                                      FROM Invoice
                                      WHERE
                                      Invoice.invoiceYear = ? AND
                                      Invoice.invoiceType='" . $invoiceType . "' AND
                                      Invoice.invoiceShopId='".$remoteShopSellerId."' AND
                                      Invoice.invoiceSiteChar= ?", [$year, $siteChar])->fetchAll()[0]['new'];

                $invoiceNew->invoiceShopId=$remoteShopSellerId;
                $invoiceNew->invoiceNumber = $number;
                $invoiceNew->invoiceSiteChar =$siteChar;
                $invoiceNew->invoiceType = $invoiceType;
                $invoiceNew->invoiceDate = $today->format('Y-m-d H:i:s');
                $todayInvoice = $today->format('d/m/Y');

                $invoiceRepo->insert($invoiceNew);
                $sectional = $number . '/' . $invoiceType;
                $documentRepo = \Monkey::app()->repoFactory->create('Document');
                // codice per inserire all'interno della cartella document
                $checkIfDocumentExist = $documentRepo->findOneBy(['number' => $number, 'year' => $year]);
                if ($checkIfDocumentExist == null) {
                    $insertDocument = $documentRepo->getEmptyEntity();
                    $insertDocument->userId = $order->userId;
                    $insertDocument->shopRecipientId = 1;
                    $insertDocument->number = $sectional;
                    $insertDocument->date = $order->orderDate;
                    $insertDocument->invoiceTypeId = $documentType;
                    $insertDocument->paydAmount = $order->paidAmount;
                    $insertDocument->paymentExpectedDate = $order->paymentDate;
                    $insertDocument->note = $order->note;
                    $insertDocument->creationDate = $order->orderDate;
                    $insertDocument->totalWithVat = $order->netTotal;
                    $insertDocument->year = $year;
                    $insertDocument->insert();
                }
                $db_con = new PDO("mysql:host={$db_host};dbname={$db_name}", $db_user, $db_pass);
                $db_con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $stmtCheckDocumentExist =$db_con->prepare("SELECT count(*) AS counterDocument from Document WHERE `number`='".$sectional."'");
                $stmtCheckDocumentExist->execute();
                while ($rowCheckDocumentExist=$stmtCheckDocumentExist->fetch(PDO::FETCH_ASSOC)){
                    if ($rowCheckDocumentExist['counterDocument']==1){
                        $doQuery=1;
                    }else{
                        $doQuery=2;
                    }
                }
                if($doQuery=='2'){
                    $remoteAddress=$userAddressRepo->findOneBy(['id'=> $order->billingAddressId]);
                        if($remoteAddress != null){
                            $remoteUserAddressId=$remoteAddress->remoteUserId;
                    }else{
                            $remoteUserAddressId='';
                    }
                    $documentRemoteUpdate=$documentRepo->findOneBy(['userId'=>$order->userId,'number'=>$sectional]);
                    $insertRemoteDocument=$db_con->prepare("INSERT INTO Document (
                                                                     userId,
                                                                     userAddressRecipientId,
                                                                     shopRecipientId,
                                                                     `number`,
                                                                     `date`,
                                                                     invoiceTypeId,
                                                                     paymentDate,
                                                                     paydAmount,
                                                                     paymentExpectedDate,
                                                                     note,
                                                                     creationDate,
                                                                     carrierId,
                                                                     totalWithVat,
                                                                     year )
                                                                    VALUES
                                                                    ( 
                                                                     '".$remoteUserAddressId."',
                                                                      '".$documentRemoteUpdate->userAddressRecipientId."',
                                                                      '".$documentRemoteUpdate->shopRecipientId."',
                                                                      '".$documentRemoteUpdate->number."',
                                                                      '".$documentRemoteUpdate->date."',
                                                                      '".$documentRemoteUpdate->invoiceTypeId."',
                                                                      '".$documentRemoteUpdate->paymentDate."',
                                                                      '".$documentRemoteUpdate->paydAmount."',
                                                                      '".$documentRemoteUpdate->paymentExpectedDate."',
                                                                      '".$documentRemoteUpdate->note."',
                                                                      '".$documentRemoteUpdate->creationDate."',
                                                                      '".$documentRemoteUpdate->carrierId."',
                                                                      '".$documentRemoteUpdate->totalWithVat."',
                                                                      '".$documentRemoteUpdate->year."'           
                                                                                )");
                }
                $stmtInvoiceExist = $db_con->prepare("SELECT 
                                     count(*) AS counterInvoice from Invoice where orderId =".$order->remoteId);
                $stmtInvoiceExist->execute();
                while ($rowInvoiceExist = $stmtInvoiceExist->fetch(PDO::FETCH_ASSOC)) {

                    if ($rowInvoiceExist['counterInvoice']=='1') {
                        $doQuery='1';
                    }else{
                        $doQuery='2';
                    }
                }
                if($doQuery=='2'){
                    $insertRemoteInvoice=$invoiceRepo->findOneBy(['orderId'=>$order->id]);
                    $stmtInvoiceInsert=$db_con->prepare("INSERT INTO  Invoice 
                                                                               (
                                                                               orderId,
                                                                               invoiceYear,
                                                                               invoiceType,
                                                                               invoiceSiteChar,
                                                                               invoiceNumber,
                                                                               invoiceDate,
                                                                               invoiceText,
                                                                               creationDate)
                                                                               VALUES(
                                                                               '".$order->remoteId."',
                                                                                '".$insertRemoteInvoice->invoiceYear."',
                                                                                '".$insertRemoteInvoice->invoiceType."',
                                                                                '".$insertRemoteInvoice->invoiceSiteChar."',
                                                                                '".$insertRemoteInvoice->invoiceNumber."',
                                                                                '".$insertRemoteInvoice->invoiceDate."',
                                                                                
                                                                                '',
                                                                                '".$insertRemoteInvoice->creationDate."'
                                                                               )
                                                                               ");
                    $stmtInvoiceInsert->execute();


                }



            } catch (\Throwable $e) {
                throw $e;
                $this->app->router->response()->raiseProcessingError();
                $this->app->router->response()->sendHeaders();
            }
        }

        foreach ($order->invoice as $invoice) {
            if (is_null($invoice->invoiceText)) {
                $userAddress = $userAddressRepo->findOneBy(['id'=>$order->billingAddressId]);
                if (!is_null($order->shipmentAddressId)) {
                    $userShipping = $userAddressRepo->findOneBy(['id'=>$order->shipmentAddressId]);
                } else {
                    $userShipping = $userAddress;
                }


                $productRepo = \Monkey::app()->repoFactory->create('ProductNameTranslation');
                if ($hasInvoice == '1') {
                    if ($isExtraUe == '1') {
                        if ($remoteShopSellerId == '44') {
                            $invoiceType = 'X';
                            $invoiceTypeVat = 'newX';
                        } else {
                            $invoiceType = $invoiceExtraUe;
                            $invoiceTypeVat = 'newX';
                        }
                        if ($changelanguage != "1") {
                            $invoiceTypeText = "Fattura N. :";
                            $invoiceHeaderText = "FATTURA";
                            $invoiceTotalDocumentText = "Totale Fattura";
                        } else {
                            $invoiceTypeText = "Invoice N. :";
                            $invoiceHeaderText = "INVOICE";
                            $invoiceTotalDocumentText = "Invoice Total";
                        }
                    } else {
                        if ($remoteShopSellerId == '44') {
                            $invoiceType = 'P';
                            $invoiceTypeVat = 'newP';
                        } else {
                            $invoiceType = $invoiceUe;
                            $invoiceTypeVat = 'newP';
                        }
                        if ($changelanguage != "1") {
                            $invoiceTypeText = "Fattura N. :";
                            $invoiceHeaderText = "FATTURA";
                            $invoiceTotalDocumentText = "Totale Fattura";
                        } else {
                            $invoiceTypeText = "Invoice N. :";
                            $invoiceHeaderText = "INVOICE";
                            $invoiceTotalDocumentText = "Invoice Total";
                        }
                    }
                } else {
                    if ($remoteShopSellerId == '44') {
                        $invoiceType = 'K';
                        $invoiceTypeVat = 'newK';
                    } else {
                        $invoiceType = $receipt;
                        $invoiceTypeVat = 'newK';
                    }
                    if ($changelanguage != "1") {

                        $invoiceTypeText = "Ricevuta N. :";
                        $invoiceHeaderText = "RICEVUTA";
                        $invoiceTotalDocumentText = "Totale Ricevuta";
                    } else {

                        $invoiceTypeText = "Receipt N. :";
                        $invoiceHeaderText = "RECEIPT";
                        $invoiceTotalDocumentText = "Receipt Total";

                    }
                }



                /*'logo' => $this->app->cfg()->fetch("miscellaneous", "logo"),
                    'fiscalData' => $this->app->cfg()->fetch("miscellaneous", "fiscalData"),*/
                $invoice->invoiceText = $view->render([
                    'app' => new CRestrictedAccessWidgetHelper($this->app),
                    'userAddress' => $userAddress,
                    'userShipping' => $userShipping,
                    'order' => $order,
                    'invoice' => $invoice,
                    'productRepo' => $productRepo,
                    'page' => $this->page,
                    'logo' => $logo,
                    'intestation' => $intestation,
                    'intestation2' => $intestation2,
                    'address' => $address,
                    'address2' => $address2,
                    'iva' => $iva,
                    'tel' => $tel,
                    'email' => $email,
                    'invoiceType' => $invoiceType,
                    'invoiceTypeVat' => $invoiceTypeVat,
                    'invoiceTypeText' => $invoiceTypeText,
                    'invoiceHeaderText' => $invoiceHeaderText,
                    'invoiceTotalDocumentText' => $invoiceTotalDocumentText,
                    'changelanguage' => $changelanguage
                ]);
                try {
                    $invoiceRepo->update($invoice);

                    if ($remoteShopSellerId == '44') {
                        $api_uid = $this->app->cfg()->fetch('fattureInCloud', 'api_uid');
                        $api_key = $this->app->cfg()->fetch('fattureInCloud', 'api_key');
                        if ($hasInvoice == '1' && $isExtraUe == '0') {
                            $insertJson = '{
  "api_uid": "' . $api_uid . '",
  "api_key": "' . $api_key . '",
  "id_cliente": "0",
  "id_fornitore": "0",
  "nome": "' . $userAddress->surname . ' ' . $userAddress->name . ' ' . $userAddress->company . '",
  "indirizzo_via": "' . $userAddress->address . '",
  "indirizzo_cap": "' . $userAddress->postcode . '",
  "indirizzo_citta": "' . $userAddress->city . '",
  "indirizzo_provincia": "' . $userAddress->province . '",
  "indirizzo_extra": "",
  "paese": "Italia",
  "paese_iso": "' . $userAddress->country->ISO . '",
  "lingua": "it",
  "piva": "' . $userAddress->fiscalCode . '",
  "cf": "' . $userAddress->fiscalCode . '",
  "autocompila_anagrafica": false,
  "salva_anagrafica": false,
  "numero": "' . $sectional . '",
  "data": "' . $todayInvoice . '",
  "valuta": "EUR",
  "valuta_cambio": 1,
  "prezzi_ivati": true,
  "rivalsa": 0,
  "cassa": 0,
  "rit_acconto": 0,
  "imponibile_ritenuta": 0,
  "rit_altra": 0,
  "marca_bollo": 0,
  "oggetto_visibile": "",
  "oggetto_interno": "",
  "centro_ricavo": "",
  "centro_costo": "",
  "note": "",
  "nascondi_scadenza": false,
  "ddt": false,
  "ftacc": false,
  "id_template": "0",
  "ddt_id_template": "0",
  "ftacc_id_template": "0",
  "mostra_info_pagamento": false,';
                            $orderPaymentMethodId = $order->orderPaymentMethodId;
                            $orderPaymentMethodTranslation = \Monkey::app()->repoFactory->create('OrderPaymentMethodTranslation')->findOneBy(['orderPaymentMethodId' => $orderPaymentMethodId, 'langId' => 1]);
                            $metodo_pagamento = $orderPaymentMethodTranslation->name;
                            switch ($orderPaymentMethodId) {
                                case 1:
                                    $metodo_titoloN = 'Merchant Paypal';
                                    $metodo_descN = $api_uid = $this->app->cfg()->fetch('payPal', 'business');
                                    break;
                                case 2:
                                    $metodo_titoloN = 'Merchant Nexi';
                                    $metodo_descN = '';
                                    break;
                                case 3:
                                    $metodo_titoloN = 'IBAN';
                                    $metodo_descN = 'IT54O0521613400000000002345';
                                    break;
                                case 5:
                                    $metodo_titoloN = '';
                                    $metodo_descN = '';
                                    break;

                            }


                            $insertJson .= '"metodo_pagamento": "' . $metodo_pagamento . '",
  "metodo_titoloN": "' . $metodo_titoloN . '",
  "metodo_descN": "' . $metodo_descN . '",
  "mostra_totali": "tutti",
  "mostra_bottone_paypal": false,
  "mostra_bottone_bonifico": false,
  "mostra_bottone_notifica": false,';
                            $insertJson .= '"lista_articoli": ';
                            $tot = 0;
                            $i = 0;
                            $scontotot = 0;
                            $articoli = [];
                            $ordinearticolo = 0;
                            foreach ($order->orderLine as $orderLine) {
                                $idlineaordine = $i + 1;
                                $idOrderLine = $orderLine->id;
                                $ordinearticolo + 1;
                                $productSku = CProductSku::defrost($orderLine->frozenProduct);
                                $codice = $orderLine->orderId . "-" . $orderLine->id;
                                $productNameTranslation = $productRepo->findOneBy(['productId' => $productSku->productId, 'productVariantId' => $productSku->productVariantId, 'langId' => '1']);
                                $nome = $productSku->productId . "-" . $productSku->productVariantId . "-" . $productSku->productSizeId;
                                $um = "";
                                $quantity = $productSku->stockQty;
                                $descrizione = (($productNameTranslation) ? $productNameTranslation->name : '') . ($orderLine->warehouseShelfPosition ? ' / ' . $orderLine->warehouseShelfPosition->printPosition() : '') . ' ' . $productSku->product->productBrand->name . ' - ' . $productSku->productId . '-' . $productSku->productVariantId . " " . $productSku->getPublicSize()->name;
                                $categoria = "";
                                $prezzo_netto = number_format($orderLine->activePrice + $orderLine->couponCharge, 2);
                                $prezzo_lordo = number_format($orderLine->activePrice, 2);
                                $scontoCharge = number_format($orderLine->couponCharge, 2);
                                $sconto = abs($scontoCharge);
                                $sconto = number_format(100 * $sconto / $orderLine->activePrice, 2);
                                $cod_iva = "0";
                                $applica_ra_contributi = "true";
                                $ordine = $ordinearticolo;
                                $sconto_rosso = "0";
                                $in_ddt = false;
                                $magazzino = true;
                                $scontotot += abs($scontoCharge);
                                $tot += $orderLine->activePrice;
                                /*  $insertLineJSon.='{
                                         "id": "'.$idlineaordine.'",
                                        "codice": "'.$codice.'",
                                        "nome": "'.$descrizione.'",
                                        "um": "",
                                        "quantita": '.$quantity.',
                                        "descrizione": "'.$descrizione.'",
                                        "categoria": "",
                                        "prezzo_netto": '.$prezzo_netto.',
                                        "prezzo_lordo": '.$prezzo_lordo.',
                                        "cod_iva": 0,
                                        "tassabile": true,
                                        "sconto": '.$sconto.',
                                        "applica_ra_contributi": true,
                                        "ordine": '.$ordine.',
                                        "sconto_rosso": 0,
                                        "in_ddt": false,
                                        "magazzino": true},
                                  ';*/
                                $articoli[] = [
                                    'id' => $idlineaordine,
                                    'codice' => $codice,
                                    'nome' => $nome,
                                    'um' => $um,
                                    'quantita' => 1,
                                    'descrizione' => $descrizione,
                                    'categoria' => $categoria,
                                    'prezzo_netto' => $prezzo_netto,
                                    'prezzo_lordo' => $prezzo_lordo,
                                    'cod_iva' => $cod_iva,
                                    'tassabile' => true,
                                    'sconto' => $sconto,
                                    'applica_ra_contributi' => $applica_ra_contributi,
                                    'ordine' => $ordine,
                                    'sconto_rosso' => $sconto_rosso,
                                    'in_ddt' => $in_ddt,
                                    'magazzino' => $magazzino
                                ];
                            }
                            $tot = number_format($tot, 2) - number_format($scontotot, 2);
                            $today = new \DateTime();
                            $dateInvoice = $today->format('d/m/Y');
                            $insertJson .= json_encode($articoli) . ',
                  
                "lista_pagamenti": [
              {
               "data_scadenza":"' . $dateInvoice . '",
               "importo": ' . number_format($tot, 2) . ',
               "metodo": "not",
               "data_saldo": "' . $dateInvoice . '" 
              }
              ],
              "ddt_numero": "",
              "ddt_data": "' . $dateInvoice . '",
              "ddt_colli": "",
              "ddt_peso": "",
              "ddt_causale": "",
              "ddt_luogo": "",
              "ddt_trasportatore": "",
              "ddt_annotazioni": "",
              "PA": false, 
              "PA_tipo_cliente": "B2B", 
              "PA_tipo": "nessuno",
              "PA_numero": "",
              "PA_data": "' . $dateInvoice . '",
              "PA_cup": "",
              "PA_cig": "",
              "PA_codice": "",
              "PA_pec": "",
              "PA_esigibilita": "N",
              "PA_modalita_pagamento": "MP01",
              "PA_istituto_credito": "",
              "PA_iban": "",
              "PA_beneficiario": "",
              "extra_anagrafica": {
                "mail": "",
                "tel": "",
                "fax": ""
              },
              "split_payment": false
            }';
                            \Monkey::app()->applicationLog('InvoiceAjaxController', 'Report', 'jsonfattureincloud', 'Json Fatture in Cloud fattura Numero' . $number . ' data:' . $dateInvoice, $insertJson);
                            $urlInsert = "https://api.fattureincloud.it/v1/fatture/nuovo";
                            $options = array(
                                "http" => array(
                                    "header" => "Content-type: text/json\r\n",
                                    "method" => "POST",
                                    "content" => $insertJson
                                ),
                            );
                            $context = stream_context_create($options);
                            $result = json_decode(file_get_contents($urlInsert, false, $context), true);
                            if (array_key_exists('success', $result)) {
                                $resultApi = "Risultato=" . $result['success'] . " new_id:" . $result['new_id'] . " token:" . $result['token'];
                            } else {
                                $resultApi = "Errore=" . $result['error'] . " codice di errore:" . $result['error_code'];
                            }
                            \Monkey::app()->applicationLog('InvoiceAjaxController', 'Report', 'ResponseApi fatture in Cloud Numero' . $sectional . ' data:' . $dateInvoice, 'Risposta FatturaincCloud', $resultApi);
                            if (array_key_exists('new_id', $result)) {
                                $fattureinCloudId = $result['new_id'];
                            }
                            if (array_key_exists('token', $result)) {
                                $fattureinCloudToken = $result['token'];

                                $updateInvoice = \Monkey::app()->repoFactory->create('Invoice')->findOneBy(['orderId' => $orderId]);
                                $updateInvoice->fattureInCloudId = $fattureinCloudId;
                                $updateInvoice->fattureInCloudToken = $fattureinCloudToken;
                                $updateInvoice->update();
                            }


                        }
                    }

                } catch (\Throwable $e) {
                    throw $e;
                    $this->app->router->response()->raiseProcessingError();
                    $this->app->router->response()->sendHeaders();


                }
            }
                $db_con = new PDO("mysql:host={$db_host};dbname={$db_name}", $db_user, $db_pass);
                $db_con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                $updateRemoteInvoice = $invoiceRepo->findOneBy(['orderId' => $order->id]);
                $invoiceTextUpdate = $updateRemoteInvoice->invoiceText;
                $stmtInvoiceUpdate = $db_con->prepare(" UPDATE Invoice SET invoiceText = :invoiceText where orderId = :remoteId");

            $stmtInvoiceUpdate->bindValue(':invoiceText', $invoice->invoiceText, PDO::PARAM_STR);
            $stmtInvoiceUpdate->bindValue(':remoteId', $order->remoteId, PDO::PARAM_INT);
            $stmtInvoiceUpdate->execute();





            return $invoice->invoiceText;
        }
    }

}

