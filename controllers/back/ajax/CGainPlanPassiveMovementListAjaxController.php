<?php

namespace bamboo\controllers\back\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\core\intl\CLang;
use bamboo\domain\entities\CGainPlan;
use bamboo\domain\entities\CGainPlanPassiveMovement;

/**
 * Class CGainPlanListAjaxController
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 30/11/2019
 * @since 1.0
 */
class CGainPlanPassiveMovementListAjaxController extends AAjaxController
{
    public function get()
    {

        $sql = 'SELECT  gppm.id as id,
                        gppm.invoice as invoice,
                        gppm.amount as amount,
                        gppm.gainPlanId as gainPlanId,
                        gppm .fornitureName as fornitureName,
                        gppm.serviceName as serviceName,
                        gppm.isActive as isActive,
                        gppm.dateCreate as dateCreate,
                        gppm.shopId as shopId,
                        gppm.dateMovement as dateMovement
        from GainPlanPassiveMovement gppm join GainPlan gp on gppm.gainPlanId=gp.id ORDER BY dateMovement DESC
        ';
        $datatable = new CDataTables($sql, ['id'], $_GET, true);
        $datatable -> doAllTheThings('true');
        $gainPlanPassiveMovements = \Monkey ::app() -> repoFactory -> create('GainPlanPassiveMovement') -> findBySql($datatable -> getQuery(), $datatable -> getParams());
        $count = \Monkey ::app() -> repoFactory -> create('GainPlanPassiveMovement') -> em() -> findCountBySql($datatable -> getQuery(true), $datatable -> getParams());
        $totalCount = \Monkey ::app() -> repoFactory -> create('GainPlanPassiveMovement') -> em() -> findCountBySql($datatable -> getQuery('full'), $datatable -> getParams());
        $invoiceRepo = \Monkey ::app() -> repoFactory -> create('Invoice');
        $orderRepo = \Monkey ::app() -> repoFactory -> create('Order');
        $orderLineRepo = \Monkey ::app() -> repoFactory -> create('OrderLine');
        $shopRepo = \Monkey ::app() -> repoFactory -> create('Shop');
        $userRepo = \Monkey ::app() -> repoFactory -> create('User');
        $countryRepo = \Monkey ::app() -> repoFactory -> create('Country');
        $gpsmRepo = \Monkey ::app() -> repoFactory -> create('GainPlanPassiveMovement');
        $seasonRepo = \Monkey ::app() -> repoFactory -> create('ProductSeason');
        $orderPaymentMethodRepo = \Monkey ::app() -> repoFactory -> create('OrderPaymentMethod');
        foreach ($datatable->getResponseSetData() as $key => $row) {
            /** @var $val CGainPlanPassiveMovement */
            $val = \Monkey ::app() -> repoFactory -> create('GainPlanPassiveMovement') -> findOneBy($row);
            $row['DT_RowId'] = $val -> printId();
            $row['id'] = '<a href="/blueseal/registri/gainplan-passivo/modifica/' . $val -> printId() . '">' . $val -> printId() . '</a>';
            $row['dateMovement'] = $val -> dateMovement;
            $row['gainPlanId'] = $val -> gainPlanId;
            $row['invoice'] = $val -> invoice;
            $row['amount'] = $val -> amount;
            $row['serviceName'] = $val -> serviceName;
            $row['fornitureName'] = $val -> fornitureName;
            $shop='';
            if ($val -> shopId != null && $val -> shopId != 0  ) {
                $shops = $shopRepo -> findOneBy(['id' => $val -> shopId]);
                $shop = $shops -> name;
            } else {
                $shop = '';
            }
            $row['shopId'] = $shop;

            $datatable->setResponseDataSetRow($key,$row);
        }

        return $datatable->responseOut();
    }
}