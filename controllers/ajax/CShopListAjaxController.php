<?php
namespace bamboo\blueseal\controllers\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\core\intl\CLang;
use bamboo\domain\entities\CShop;

/**
 * Class CShopListAjaxController
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
class CShopListAjaxController extends AAjaxController
{
    public function get()
    {
        $datatable = new CDataTables("Shop",['id'],$_GET,false);
        $datatable->addCondition('id',$this->app->repoFactory->create('Shop')->getAutorizedShopsIdForUser());

        $shops = $this->app->repoFactory->create('Shop')->findBySql($datatable->getQuery(),$datatable->getParams());
        $count = $this->app->repoFactory->create('Shop')->em()->findCountBySql($datatable->getQuery(true), $datatable->getParams());
        $totalCount = $this->app->repoFactory->create('Shop')->em()->findCountBySql($datatable->getQuery('full'), $datatable->getParams());

        $response = [];
        $response ['draw'] = $_GET['draw'];
        $response ['recordsTotal'] = $totalCount;
        $response ['recordsFiltered'] = $count;
        $response ['data'] = [];
        /** @var CShop $shop */
        foreach($shops as $shop){
            $row = [];
            $row['DT_RowId'] = $shop->printId();
            $row['id'] = '<a href="/blueseal/shop?id='.$shop->printId().'">'.$shop->printId().'</a>';
            $row['title'] = $shop->title;
            $row['owner'] = $shop->owner;
            $row['currentSeasonMultiplier'] = $shop->currentSeasonMultiplier;
            $row['pastSeasonMultiplier'] = $shop->pastSeasonMultiplier;
            $row['referrerEmails'] = implode('<br />',explode(';',$shop->referrerEmails));
            $row['saleMultiplier'] = $shop->saleMultiplier;
            $row['minReleasedProducts'] = $shop->minReleasedProducts;
            $row['isActive'] = $shop->isActive;
            $users = [];
            foreach ($shop->user as $user) {
                $users[] = $user->email;
            }
            $row['users'] = implode('<br />',$users);
            $row['iban'] = $shop->iban;

            $response['data'][] = $row;
        }
        return json_encode($response);
    }
}