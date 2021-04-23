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
use Exception;
use Throwable;

/**
 * Class CSendInvoiceLegalAjaxController
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 06/05/2020
 * @since 1.0
 */
class CSendInvoiceLegalAjaxController extends AAjaxController
{


    public function post()
    {
        $resultApi='';
        $data = \Monkey::app()->router->request()->getRequestData();
        $invoiceId = $data['billRegistryInvoiceId'];
        $paymentBillRepo = \Monkey::app()->repoFactory->create('BillRegistryActivePaymentSlip');
        $billRegistryTimeTableRepo = \Monkey::app()->repoFactory->create('BillRegistryTimeTable');
        $billRegistryInvoiceRepo = \Monkey::app()->repoFactory->create('BillRegistryInvoice');
        $countryRepo=\Monkey::app()->repoFactory->create('Country');
        $billRegistryClientRepo = \Monkey::app()->repoFactory->create('BillRegistryClient');
        $billRegistryTypePaymentRepo = \Monkey::app()->repoFactory->create('BillRegistryTypePayment');
        $billRegistryPaymentSlipRepo = \Monkey::app()->repoFactory->create('BillRegistryActivePaymentSlipStatus');
        $billRegistryClientAccountRepo = \Monkey::app()->repoFactory->create('BillRegistryClientAccount');
        $billRegistryInvoiceRowRepo=\Monkey::app()->repoFactory->create('BillRegistryInvoiceRow');
        $invoice=$billRegistryInvoiceRepo->findOneBy(['id'=>$invoiceId]);
        $client=$billRegistryClientRepo->findOneBy(['id'=>$invoice->billRegistryClientId]);
        $account=$billRegistryClientAccountRepo->findOneBy(['billRegistryClientId'=>$invoice->billRegistryClientId]);
        $paymentType=$billRegistryTypePaymentRepo->findOneBy(['id'=>$invoice->billRegistryTypePaymentId]);
        $country=$countryRepo->findOneBy(['id'=>$client->countryId]);

        $rowInvoice=$billRegistryInvoiceRowRepo->findBy(['billRegistryInvoiceId'=>$invoice->id]);
        $dateInvoice = strtotime($invoice->invoiceDate);
        $todayInvoice = date('d/m/Y',$dateInvoice);



        try {
            $api_uid = $this->app->cfg()->fetch('fattureInCloud','api_uid');
            $api_key = $this->app->cfg()->fetch('fattureInCloud','api_key');
                $insertJson = '{
                          "api_uid": "' . $api_uid . '",
                          "api_key": "' . $api_key . '",
                          "id_cliente": "0",
                          "id_fornitore": "0",
                          "nome": "' . $client->companyName  . '",
                          "indirizzo_via": "' . $client->address . '",
                          "indirizzo_cap": "' . $client->zipcode . '",
                          "indirizzo_citta": "' . $client->city . '",
                          "indirizzo_provincia": "' . $client->province . '",
                          "indirizzo_extra": "' . $client->extra . '",
                          "paese": "Italia",
                          "paese_iso": "' . $country->ISO . '",
                          "lingua": "it",
                          "piva": "' . $client->vatNumber . '",
                          "cf": "' . $client->vatNumber . '",
                          "autocompila_anagrafica": false,
                          "salva_anagrafica": false,
                          "numero": "' . $invoice->invoiceNumber .'/'.$invoice->invoiceType. '",
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



                $insertJson .= '"metodo_pagamento": "' . $paymentType->name . '",
                          "metodo_titoloN": "",
                          "metodo_descN": "",
                          "mostra_totali": "tutti",
                          "mostra_bottone_paypal": false,
                          "mostra_bottone_bonifico": false,
                          "mostra_bottone_notifica": false,';
                $insertJson .= '"lista_articoli": ';
                $tot = 0;
                $i = 0;
                $scontotot = 0;
                $articoli = [];
                $ordineArticolo = 0;
                $idlineaordine=0;
                $productRepo=\Monkey::app()->repoFactory->create('BillRegistryProduct');
                foreach ($rowInvoice as $row) {
                    $idlineaordine = $idlineaordine + 1;
                    $ordineArticolo=$ordineArticolo + 1;
                    $product=$productRepo->findOneBy(['id'=>$row->billRegistryProductId]);
                    if($product) {
                        $codice = $product->codeProduct;
                        $nome = $product->nameProduct;
                        $um = $product->um;
                    }else{
                        $codice=999;
                        $nome='servizi vari';
                        $um='nr';
                    }

                    $nome = $product->nameProduct;

                    $quantity = $row->qty;
                    $descrizione = $row->description;
                    $categoria = "";
                    $prezzo_netto = number_format($row->netPriceRow,2,'.','');
                    $prezzo_lordo = number_format($row->grossTotalRow,2,'.','');
                    $scontoCharge = $row->percentDiscount;
                    $sconto = $row->discountRow;
                    $cod_iva = "0";
                    $applica_ra_contributi = "true";
                    $ordine = $ordineArticolo;
                    $sconto_rosso = "0";
                    $in_ddt = false;
                    $magazzino = true;


                    $articoli[] = [
                        'id' => $idlineaordine,
                        'codice' => $codice,
                        'nome' => $nome,
                        'um' => $um,
                        'quantita' => $row->qty,
                        'descrizione' => $descrizione,
                        'categoria' => $categoria,
                        'prezzo_netto' => $prezzo_netto,
                        'prezzo_lordo' => $prezzo_lordo,
                        'cod_iva' => $cod_iva,
                        'tassabile' => true,
                        'sconto' => $sconto,
                        'applica_ra_contributi' => $applica_ra_contributi,
                        'ordine' => $ordineArticolo,
                        'sconto_rosso' => $sconto_rosso,
                        'in_ddt' => $in_ddt,
                        'magazzino' => $magazzino
                    ];
                }
                $tot = number_format($invoice->grossTotal,2,'.','');

                $insertJson .= json_encode($articoli) . ',';
 $insertJson .= '"lista_pagamenti": ';

                                       // "lista_pagamenti": [';
                        $payments=$billRegistryTimeTableRepo->findBy(['billRegistryInvoiceId'=>$invoice->id]);
                        foreach($payments as $payment ) {
                            $dateInvoicePayment = strtotime($payment->dateEstimated);
                            $dateEstimated = date('d/m/Y',$dateInvoicePayment);
                            $lista_pagamenti[] = [
                                'data_scadenza' => $dateEstimated,
                                'importo' => number_format($payment->amountPayment,2,'.',''),
                                'metodo' => 'not',
                                'data_saldo' => ''
                            ];
                        }
                 $insertJson.=  json_encode($lista_pagamenti) ;

                                      $insertJson.=',
                                      "ddt_numero": "",
                                      "ddt_data": "' . $todayInvoice . '",
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
                                      "PA_data": "' . $todayInvoice . '",
                                      "PA_cup": "",
                                      "PA_cig": "",
                                      "PA_codice": "",
                                      "PA_pec": "",
                                      "PA_esigibilita": "N",
                                      "PA_modalita_pagamento": "'.$paymentType->codice_modalita_pagamento_fe.'",
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
                \Monkey::app()->applicationLog('CSendInvoiceLegalAjaxController','Report','jsonfattureincloud','Json Fatture in Cloud fattura Numero' . $invoice->invoiceNumber .'/'.$invoice->invoiceType.  ' data:' . $dateInvoice,$insertJson);
                $urlInsert = "https://api.fattureincloud.it/v1/fatture/nuovo";
                $options = array(
                    "http" => array(
                        "header" => "Content-type: text/json\r\n",
                        "method" => "POST",
                        "content" => $insertJson
                    ),
                );

                $context = stream_context_create($options);
                $result = json_decode(file_get_contents($urlInsert,false,$context),true);
                if (array_key_exists('success',$result)) {
                    $resultApi = "Risultato=" . $result['success'] . " new_id:" . $result['new_id'] . " token:" . $result['token'];
                } else {
                    $resultApi = "Errore=" . $result['error'] . " codice di errore:" . $result['error_code'];
                }
                \Monkey::app()->applicationLog('CSendInvoiceLegalAjaxController','Report','ResponseApi fatture in Cloud Numero' . $invoice->invoiceNumber .'/'.$invoice->invoiceType. ' data:' . $dateInvoice,'Risposta FatturaincCloud',$resultApi);
                if (array_key_exists('new_id',$result)) {
                    $fattureinCloudId = $result['new_id'];
                }
                if (array_key_exists('token',$result)) {
                    $fattureinCloudToken = $result['token'];

                    $updateInvoice = \Monkey::app()->repoFactory->create('BillRegistryInvoice')->findOneBy(['id' => $invoice->id,]);
                    $updateInvoice->idFattureInCloud = $fattureinCloudId;
                    $updateInvoice->isBilled = 1;
                    $updateInvoice->update();
                }

return 'Inserimento su fatture in cloud eseguito con successo';



        } catch (\Exception $e) {
            \Monkey::app()->applicationLog('CSendInvoiceLegalAjaxController','error','Inserimento fattura su fattureincloud', $e->getMessage(),$e->getCode());
return 'Problema con l\'inserimento codice errore '.$resultApi;

        }
    }
}


