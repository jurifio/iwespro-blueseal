<?php
namespace bamboo\blueseal\controllers\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\core\intl\CLang;

/**
 * Class CProductListAjaxController
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
class CFriendsListAjaxController extends AAjaxController
{
    public function get()
    {
        $shopIds = $this->app->repoFactory->create('Shop')->getAutorizedShopsIdForUser();

        $datatable = new CDataTables('vBluesealFriendsList',['id'],$_GET);
        $datatable->addSearchColumn('shop',$shopIds);

        $orribilità = $this->app->dbAdapter->query($datatable->getQuery(false,true),$datatable->getParams())->fetchAll();
        $count = $this->app->dbAdapter->query($datatable->getQuery(true,true),$datatable->getParams())->fetch();
        $totalCount = $this->app->dbAdapter->query($datatable->getQuery('full',true),$datatable->getParams())->fetch();

        /*$brands = $this->app->repoFactory->create('ProductBrand')->em()->findBySql($datatable->getQuery(),$datatable->getParams());
        $count = $this->app->repoFactory->create('ProductBrand')->em()->findCountBySql($datatable->getQuery(true), $datatable->getParams());
        $totalCount = $this->app->repoFactory->create('ProductBrand')->em()->findCountBySql($datatable->getQuery('full'), $datatable->getParams());
        */
        $response = [];
        $response ['draw'] = $_GET['draw'];
        $response ['recordsTotal'] = $totalCount;
        $response ['recordsFiltered'] = $count;
        $response ['data'] = $orribilità;

        foreach($response['data'] as $k => $v) {
            if ((0 == $v['pubblicati']) || (0 == $v['pubblicati'])) $percentPublished = '-';
            else $percentPublished  = round($v['pubblicati'] / $v['prodotti'] * 100, 2) . '%';
            $response['data'][$k]['pubblicati'] .= ' <span class="small">(' . $percentPublished . ')</span>';
        }

        return json_encode($response);
    }
}