<?php
namespace bamboo\blueseal\controllers\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\core\intl\CLang;


/**
 * Class CProductDetailListAjaxController
 * @package bamboo\blueseal\controllers\ajax
 *
 * @author Bambooshoot Team <emanuele@bambooshoot.agency>
 *
 * @copyright (c) Bambooshoot snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date $date
 * @since 1.0
 */
class CNewsletterEmailListAjaxController extends AAjaxController
{
    protected $urls = [];
    protected $authorizedShops = [];
    protected $em;

    /**
     * @param $action
     * @return mixed
     */
    public function createAction($action)
    {
        $this->app->setLang(new CLang(1,'it'));
        $this->urls['base'] = $this->app->baseUrl(false)."/blueseal/";
        $this->urls['page'] = $this->urls['base']."prodotti";
        $this->urls['dummy'] = $this->app->cfg()->fetch('paths','dummyUrl');

        $this->em = new \stdClass();
        $this->em->newsletter = $this->app->entityManagerFactory->create('newsletter');

        return $this->{$action}();
    }

    public function get()
    {
        $datatable = new CDataTables('vBluesealNewsletterEmailList',['id'],$_GET);
        $users = $this->app->repoFactory->create('User');

        $newsletter = $this->app->repoFactory->create('Newsletter')->em()->findBySql($datatable->getQuery(),$datatable->getParams());
        $count = $this->em->newsletter->findCountBySql($datatable->getQuery(true), $datatable->getParams());
        $totalCount = $this->em->newsletter->findCountBySql($datatable->getQuery('full'), $datatable->getParams());


        $response = [];
        $response ['draw'] = $_GET['draw'];
        $response ['recordsTotal'] = $totalCount;
        $response ['recordsFiltered'] = $count;
        $response ['data'] = [];

        $i = 0;

        foreach($newsletter as $val){
            \BlueSeal::dump($val);
            $user = $users->findOneBy(["email" => $val->email]);
            \BlueSeal::dump($user);
			try {
				$response['data'][$i]["DT_RowId"] = 'row__' . $val->email;
				$response['data'][$i]["DT_RowClass"] = 'colore';
				$response['data'][$i]['email'] = $val->email;
				$response['data'][$i]['name'] = ($val->name) ? $val->name : '-';
                $response['data'][$i]['surname'] = ($val->surname) ? $val->surname : `-`;
                $response['data'][$i]['isActive'] = ($val->isActive) ? "Attiva" : "Non Attiva";
                $response['data'][$i]['subscriptionDate'] = ($val->subscriptionDate) ? $val->subscriptionDate : "-";
                $response['data'][$i]['subscriptionDate'] = ($val->unsubscriptionDate) ? $val->unsubscriptionDate : "-";
				$i++;
			} catch (\Exception $e) {
				throw $e;
			}

        }

        return json_encode($response);
    }
}