<?php

namespace bamboo\controllers\back\ajax;

use bamboo\domain\entities\CUserAddress;
use bamboo\ecommerce\views\VBase;
use bamboo\blueseal\business\CBlueSealPage;
use bamboo\core\theming\CRestrictedAccessWidgetHelper;
use bamboo\domain\entities\CProductSku;
use bamboo\domain\entities\CUser;

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
        $order = $orderRepo->findOneBy(['id' => $orderId]);
        $BillingUserAddress = CUserAddress::defrost($order->frozenBillingAddress);
        $extraUe=$BillingUserAddress->countryId;
        $countryRepo=\Monkey::app()->repoFactory->create('Country');
        $findIsExtraUe=$countryRepo->findOneBy(['id'=>$extraUe]);
        $isExtraUe=$findIsExtraUe->extraue;

            if($extraUe!='110'){
            $changelanguage="1";

            }else{
                $changelanguage="0";
            }



        $hasInvoice = $order->hasInvoice;
        $invoiceRepo = \Monkey::app()->repoFactory->create('Invoice');
        $invoiceNew = $invoiceRepo->getEmptyEntity();
        $siteChar = $this->app->cfg()->fetch("miscellaneous", "siteInvoiceChar");
        if ($order->invoice->isEmpty()) {
            try {
                $invoiceNew->orderId = $orderId;
                $today = new \DateTime();
                $invoiceNew->invoiceYear = $today->format('Y-m-d H:i:s');
                $year = (new \DateTime())->format('Y');
                $em = $this->app->entityManagerFactory->create('Invoice');
               if($hasInvoice =='1') {
                    if($isExtraUe=='1') {
                        $invoiceType = 'X';
                        $documentType='17';
                        if($changelanguage!="1") {
                            $invoiceTypeText = "Fattura N. :";
                            $invoiceHeaderText = "FATTURA";
                            $invoiceTotalDocumentText = "Totale Fattura";
                            $documentType='18';
                        }else{
                            $invoiceTypeText = "Invoice N. :";
                            $invoiceHeaderText = "INVOICE";
                            $invoiceTotalDocumentText = "Invoice Total";



                        }
                    }else{
                        $invoiceType ='P';
                        $documentType='17';
                        if($changelanguage!="1") {
                            $invoiceTypeText = "Fattura N. :";
                            $invoiceHeaderText = "FATTURA";
                            $invoiceTotalDocumentText = "Totale Fattura";
                        }else {
                            $invoiceTypeText = "Invoice N. :";
                            $invoiceHeaderText = "INVOICE";
                            $invoiceTotalDocumentText = "Invoice Total";
                        }
                    }
               }else{
                   if($changelanguage!="1") {
                       $documentType='16';
                       $invoiceType = 'K';
                       $invoiceTypeText = "Ricevuta N. :";
                       $invoiceHeaderText = "RICEVUTA FISCALE";
                       $invoiceTotalDocumentText = "Totale Ricevuta";
                   }else{
                       $documentType='16';
                       $invoiceType = 'K';
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
                                      Invoice.invoiceSiteChar= ?", [$year, $siteChar])->fetchAll()[0]['new'];

                $invoiceNew->invoiceNumber = $number;
                $invoiceNew->invoiceType = $invoiceType;
                $invoiceNew->invoiceDate = $today->format('Y-m-d H:i:s');
                $todayInvoice=$today->format('d/m/Y');

                $invoiceRepo->insert($invoiceNew);
                $sectional=$number.'/'.$invoiceType;
                $documentRepo=\Monkey::app()->repoFactory->create('Document');
                $checkIfDocumentExist=$documentRepo->findOneBy(['number'=>$number,'year'=>$year]);
                if($checkIfDocumentExist == null){
                    $insertDocument=$documentRepo->getEmptyEntity();
                    $insertDocument->userId=$order->userId;
                    $insertDocument->shopRecipientId=1;
                    $insertDocument->number=$sectional;
                    $insertDocument->date=$order->orderDate;
                    $insertDocument->invoiceTypeId=$documentType;
                    $insertDocument->paydAmount=$order->paidAmount;
                    $insertDocument->paymentExpectedDate=$order->paymentDate;
                    $insertDocument->note=$order->note;
                    $insertDocument->creationDate=$order->orderDate;
                    $insertDocument->totalWithVat=$order->netTotal;
                    $insertDocument->year=$year;
                    $insertDocument->insert();
                }
                $order = $orderRepo->findOneBy(['id' => $orderId]);
            } catch (\Throwable $e) {
                throw $e;
                $this->app->router->response()->raiseProcessingError();
                $this->app->router->response()->sendHeaders();
            }
        }

        foreach ($order->invoice as $invoice) {
            if (is_null($invoice->invoiceText)) {
                $userAddress = CUserAddress::defrost($order->frozenBillingAddress);
                if (!is_null($order->frozenShippingAddress)) {
                    $userShipping = CUserAddress::defrost($order->frozenShippingAddress);
                } else {
                    $userShipping = $userAddress;
                }


                $productRepo = \Monkey::app()->repoFactory->create('ProductNameTranslation');
                if($hasInvoice =='1') {
                    if($isExtraUe=='1') {
                        $invoiceType = 'X';
                        if($changelanguage!="1") {
                            $invoiceTypeText = "Fattura N. :";
                            $invoiceHeaderText = "FATTURA";
                            $invoiceTotalDocumentText = "Totale Fattura";
                        }else{
                            $invoiceTypeText = "Invoice N. :";
                            $invoiceHeaderText = "INVOICE";
                            $invoiceTotalDocumentText = "Invoice Total";
                        }
                    }else{
                        $invoiceType='P';
                        if($changelanguage!="1") {
                            $invoiceTypeText = "Fattura N. :";
                            $invoiceHeaderText = "FATTURA";
                            $invoiceTotalDocumentText = "Totale Fattura";
                        }else {
                            $invoiceTypeText = "Invoice N. :";
                            $invoiceHeaderText = "INVOICE";
                            $invoiceTotalDocumentText = "Invoice Total";
                        }
                    }
                }else{
                    if($changelanguage!="1") {
                        $invoiceType = 'K';
                        $invoiceTypeText = "Ricevuta N. :";
                        $invoiceHeaderText = "RICEVUTA FISCALE";
                        $invoiceTotalDocumentText = "Totale Ricevuta";
                    }else{
                        $invoiceType = 'K';
                        $invoiceTypeText = "Receipt N. :";
                        $invoiceHeaderText = "RECEIPT";
                        $invoiceTotalDocumentText = "Receipt Total";

                    }
                }
                $invoice->invoiceText = $view->render([
                    'app' => new CRestrictedAccessWidgetHelper($this->app),
                    'userAddress' => $userAddress,
                    'userShipping' => $userShipping,
                    'order' => $order,
                    'invoice' => $invoice,
                    'productRepo' => $productRepo,
                    'page' => $this->page,
                    'logo' => $this->app->cfg()->fetch("miscellaneous", "logo"),
                    'fiscalData' => $this->app->cfg()->fetch("miscellaneous", "fiscalData"),
                    'invoiceType'=>$invoiceType,
                    'invoiceTypeText' => $invoiceTypeText,
                    'invoiceHeaderText' => $invoiceHeaderText,
                    'invoiceTotalDocumentText'=> $invoiceTotalDocumentText,
                    'changelanguage'=>$changelanguage
                ]);
                try {
                    $invoiceRepo->update($invoice);
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
  "piva": "' .$userAddress->fiscalCode . '",
  "cf": "' .$userAddress->fiscalCode . '",
  "autocompila_anagrafica": false,
  "salva_anagrafica": false,
  "numero": "'.$number.'",
  "data": "'.$todayInvoice.'",
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
                        $articoli=[];

                        foreach ($order->orderLine as $orderLine) {
                            $idlineaordine=$i+1;
                            $idOrderLine = $orderLine->id;

                            $productSku = CProductSku::defrost($orderLine->frozenProduct);
                            $codice=$orderLine->orderId."-".$orderLine->id;
                            $productNameTranslation = $productRepo->findOneBy(['productId' => $productSku->productId, 'productVariantId' => $productSku->productVariantId, 'langId' => '1']);
                            $nome = $productSku->productId . "-" . $productSku->productVariantId . "-" . $productSku->productSizeId;
                            $um = "";
                            $quantity = $productSku->stockQty;
                            $descrizione = (($productNameTranslation) ? $productNameTranslation->name : '') . ($orderLine->warehouseShelfPosition ? ' / ' . $orderLine->warehouseShelfPosition->printPosition() : '') . ' ' . $productSku->product->productBrand->name . ' - ' . $productSku->productId . '-' . $productSku->productVariantId." ".$productSku->getPublicSize()->name;
                            $categoria="";
                            $prezzo_netto=number_format($orderLine->activePrice+$orderLine->couponCharge,2);
                            $prezzo_lordo=number_format($orderLine->activePrice,2);
                            $sconto=number_format($orderLine->couponCharge);
                            $cod_iva="0";
                            $applica_ra_contributi="true";
                            $ordine=$order->id;
                            $sconto_rosso="0";
                            $in_ddt=false;
                            $magazzino=true;

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
                                'um'=> $um,
                                'quantita' => 1,
                                'descrizione' =>$descrizione,
                                'categoria' => $categoria,
                                'prezzo_netto' =>$prezzo_netto,
                                'prezzo_lordo' =>$prezzo_lordo,
                                'cod_iva' => $cod_iva,
                                'tassabile' => true,
                                'sconto' => $sconto,
                                'applica_ra_contributi'=>$applica_ra_contributi,
                                'ordine'=> $ordine,
                                'sconto_rosso' =>$sconto_rosso,
                                'in_ddt' => $in_ddt,
                                'magazzino'=>$magazzino
                            ];
                        }
                        $tot = number_format($tot,2);
                        $today = new \DateTime();
                        $dateInvoice = $today->format('d/m/Y');
                        $insertJson.= json_encode($articoli).',
                  
                "lista_pagamenti": [
              {
               "data_scadenza":"'.$dateInvoice.'",
               "importo": '.$tot.',
               "metodo": "not",
               "data_saldo": "'.$dateInvoice.'" 
              }
              ],
              "ddt_numero": "",
              "ddt_data": "'.$dateInvoice.'",
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
              "PA_data": "'.$dateInvoice.'",
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

                        $urlInsert = "https://api.fattureincloud.it/v1/fatture/nuovo";
                        $options = array(
                            "http" => array(
                                "header"  => "Content-type: text/json\r\n",
                                "method"  => "POST",
                                "content" => $insertJson
                            ),
                        );
                        $context  = stream_context_create($options);
                        $result = json_decode(file_get_contents($urlInsert, false, $context), true);
                        \Monkey::app()->applicationLog('InvoiceAjaxController', 'alert', 'ResponseApi fatture in Cloud', $result);
                        $fattureinCloudId=$result['new_id'];
                        $fattureinCloudToken=$result['token'];
                        $updateInvoice=\Monkey::app()->repoFactory->create('Invoice')->findOneBy(['orderId'=>$orderId]);
                        $updateInvoice->fattureInCloudId=$fattureinCloudId;
                        $updateInvoice->fattureInCloudToken=$fattureinCloudToken;
                        $updateInvoice->update();


                    }

                } catch (\Throwable $e) {
                    throw $e;
                    $this->app->router->response()->raiseProcessingError();
                    $this->app->router->response()->sendHeaders();


                }
            }


            return $invoice->invoiceText;
        }
    }

}

