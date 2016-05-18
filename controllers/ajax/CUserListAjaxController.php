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
class CUserListAjaxController extends AAjaxController
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

        if ($this->app->getUser()->hasPermission('allShops')) {

        } else{
            $res = $this->app->dbAdapter->select('UserHasShop',['userId'=>$this->app->getUser()->getId()])->fetchAll();
            foreach($res as $val) {
                $this->authorizedShops[] = $val['shopId'];
            }
        }

        $this->em = new \stdClass();
        $this->em->products = $this->app->entityManagerFactory->create('Product');

        return $this->{$action}();
    }

    public function get()
    {
        $datatable = new CDataTables('vBluesealUsers',['id'],$_GET);

        $users = $this->app->repoFactory->create('User')->em()->findBySql($datatable->getQuery(),$datatable->getParams());
        $count = $this->em->products->findCountBySql($datatable->getQuery(true), $datatable->getParams());
        $totalCount = $this->em->products->findCountBySql($datatable->getQuery('full'), $datatable->getParams());

        $response = [];
        $response ['draw'] = $_GET['draw'];
        $response ['recordsTotal'] = $totalCount;
        $response ['recordsFiltered'] = $count;
        $response ['data'] = [];

        $i = 0;

        foreach($users as $val)
        {
            $response['data'][$i]["DT_RowId"] = 'row__'.$val->id;
            $response['data'][$i]["DT_RowClass"] = $val->isActive == 1 ? 'active' : 'unactive' ;;
            $response['data'][$i]['name'] = $val->userDetails->name;
            $response['data'][$i]['surname'] = $val->userDetails->surname;
            $response['data'][$i]['email'] = $val->email;
            $response['data'][$i]['method'] = $val->registrationEntryPoint;
            $response['data'][$i]['sex'] = $val->userDetails->gender == 'M' ? 'Uomo' : 'Donna';
            $color = $val->isActive == 1 ? '#008200' : '';
            $icon = "fa-user";
            if(isset($val->rbacRole) && !$val->rbacRole->isEmpty() ){
                $color =  "#cbac59";
                if($val->rbacRole->findOneByKey('title','sa')){
                    $icon = "fa-user-secret";
                }
            }
            $response['data'][$i]['status'] = '<i style="color: '.$color.'" class="fa '.$icon.'"></i>';
            $response['data'][$i]['creationDate'] = $val->creationDate;
            $i++;
        }

        return json_encode($response);
    }
}