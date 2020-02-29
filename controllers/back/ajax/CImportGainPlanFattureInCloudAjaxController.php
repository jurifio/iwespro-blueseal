<?php

namespace bamboo\controllers\back\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\core\intl\CLang;
use bamboo\domain\entities\CGainPlan;
use bamboo\domain\entities\CGainPlanPassiveMovement;

/**
 * Class CImportGainPlanFattureInCloudAjaxController
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 29/02/2020
 * @since 1.0
 */

class CImportGainPlanFattureInCloudAjaxController extends AAjaxController
{
    public function get()
    {

        try {
            $invoiceRepo = \Monkey::app()->repoFactory->create('Invoice');
            $orderRepo = \Monkey::app()->repoFactory->create('Order');
            $orderLineRepo = \Monkey::app()->repoFactory->create('OrderLine');
            $shopRepo = \Monkey::app()->repoFactory->create('Shop');
            $userRepo = \Monkey::app()->repoFactory->create('User');
            $countryRepo = \Monkey::app()->repoFactory->create('Country');
            $gpsmRepo = \Monkey::app()->repoFactory->create('GainPlanPassiveMovement');
            $seasonRepo = \Monkey::app()->repoFactory->create('ProductSeason');
            $orderPaymentMethodRepo = \Monkey::app()->repoFactory->create('OrderPaymentMethod');
            $gainPlanRepo = \Monkey::app()->repoFactory->create('GainPlan');
            $yearNow = date('Y');
            $yearNow='2019';
            $api_uid = '34021';
            $api_key = '443884d05056b5f0831446538c6e840f';
            $insertJson = '{
  "api_uid": "34021",
  "api_key": "443884d05056b5f0831446538c6e840f",
  "anno": 2019,
  "data_inizio": "01/01/' . $yearNow . '",
  "data_fine": "31/12/' . $yearNow . '",
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
                    "header" => "Content-type: text/json\r\n",
                    "method" => "POST",
                    "content" => $insertJson
                ),
            );
            $context = stream_context_create($options);
            $result = json_decode(file_get_contents($urlInsert,false,$context));
            foreach ($result->lista_documenti as $val) {
                if ($val->tipo == 'fatture') {
                    $gainPlanFind = \Monkey::app()->repoFactory->create('GainPlan')->findOneBy(['externalId' => $val->id]);
                    if ($gainPlanFind == null) {
                        $gainPlanInsert = $gainPlanRepo->getEmptyEntity();

                        $gainPlanInsert->customerName = $val->nome;
                        $dateCheck=strtotime($val->data);
                        $newdate=str_replace('/','-',$val->data);
                        $date = new \DateTime($newdate);
                        $seasons = $seasonRepo->findAll();
                        foreach ($seasons as $season) {
                            $dateStart = strtotime($season->dateStart);
                            $dateEnd = strtotime($season->dateEnd);
                            if ($dateCheck >= $dateStart && $dateCheck <= $dateEnd) {
                                $seasonId = $season->id;
                                $dateInvoice =$date->format('Y-m-d H:i:s');
                            }
                        }
                        $gainPlanInsert->seasonId=$seasonId;
                        $gainPlanInsert->dateMovement=$dateInvoice;
                        $gainPlanInsert->amount = $val->importo_netto;
                        $gainPlanInsert->invoiceExternal = $val->numero;
                        $gainPlanInsert->externalId = $val->id;
                        $gainPlanInsert->TypeMovement = 2;
                        $gainPlanInsert->isActive=1;
                        $gainPlanInsert->insert();
                    }
                }


            }

        } catch (\Throwable $e) {
            \Monkey::app()->applicationLog('CImportGainPlanFattureInCloudAjaxController','error','Import Gain Plan Error',$e);
        }


        return 'prova';
    }
}