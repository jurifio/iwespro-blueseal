<?php
namespace bamboo\controllers\back\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\core\intl\CLang;
use bamboo\domain\entities\CBillRegistryClient;
use bamboo\domain\entities\CShop;
use bamboo\domain\entities\CAddressBook;

/**
 * Class CBillRegistryClientListAjaxController
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
class CBillRegistryClientListAjaxController extends AAjaxController
{
    public function get()
    {
        $datatable = new CDataTables("BillRegistryClient",['id'],$_GET,false);
        $datatable->addCondition('id',\Monkey::app()->repoFactory->create('Shop')->getAutorizedShopsIdForUser());

        $billRegistryClients = \Monkey::app()->repoFactory->create('BillRegistryClient')->findBySql($datatable->getQuery(),$datatable->getParams());
        $count = \Monkey::app()->repoFactory->create('BillRegistryClient')->em()->findCountBySql($datatable->getQuery(true), $datatable->getParams());
        $totalCount = \Monkey::app()->repoFactory->create('BillRegistryClient')->em()->findCountBySql($datatable->getQuery('full'), $datatable->getParams());

        $response = [];
        $response ['draw'] = $_GET['draw'];
        $response ['recordsTotal'] = $totalCount;
        $response ['recordsFiltered'] = $count;
        $response ['data'] = [];
        $addressBookRepo=\Monkey::app()->repoFactory->create('AddressBook');
        /** @var CBillRegistryClient $client */
        foreach($billRegistryClients as $client){

                $row = [];
                $row['DT_RowId'] = $client->printId();
                $row['id'] = '<a href="/blueseal/anagrafica/clienti-modifica?id=' . $client->printId() . '">' . $client->printId() . '</a>';
                $row['companyName'] = $client->companyName;


                $response['data'][] = $row;

        }
        return json_encode($response);
    }
}