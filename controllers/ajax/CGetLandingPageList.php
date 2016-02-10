<?php
namespace bamboo\blueseal\controllers\ajax;

use bamboo\ecommerce\views\VBase;
use redpanda\blueseal\business\CJsonDataTables;
use bamboo\core\intl\CLang;
use bamboo\core\io\CJsonAdapter;

/**
 * Class CGetLandingPageList
 * @package redpanda\blueseal\controllers\ajax
 *
 * @author Bambooshoot Team <emanuele@bambooshoot.agency>, 10/22/2015
 *
 * @copyright (c) Bambooshoot snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @since 1.0
 */
class CGetLandingPageList extends AAjaxController
{
    /**
     * @var array
     */
    protected $urls = [];
    /**
     * @var array
     */
    protected $authorizedShops = [];

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

        if ($this->app->getUser()->hasRole('ownerEmployee')) {

        } else if($this->app->getUser()->hasRole('friendEmployee')){
            $res = $this->app->dbAdapter->select('UserHasShop',['userId'=>$this->app->getUser()->getId()])->fetchAll();
            foreach($res as $val) {
                $this->authorizedShops[] = $val['shopId'];
            }
        }

        return $this->{$action}();
    }

    /**
     * @return string
     */
    public function get()
    {
        $view = new VBase(array());
        $view->setTemplatePath($this->app->cfg()->fetch('paths','blueseal').'/template/widgets/section.php');
        $root = $this->app->cfg()->fetch('paths','root');
        $this->app->setLang(new CLang(1,'it'));

        $json = new CJsonAdapter($root.'/htdocs/pickyshop/content/themes/flatize/layout/focusPage.it.json');
        $dataTable = new CJsonDataTables($_GET, $json);

        $filteredJson = $json->prepare($dataTable->getQuery(),$dataTable->getParams());

        $response ['draw'] = $_GET['draw'];
        $response ['recordsTotal'] = $json->count();
        $response ['recordsFiltered'] = $json->getSqlFilteredCount();
        $response ['data'] = [];

        $i = 0;
        foreach($filteredJson as $key => $landingPage) {

            $creationDate = new \DateTime($landingPage['creationDate']);
            $updateDate = new \DateTime($landingPage['updateDate']);

            $response['data'][$i]["DT_RowId"] = 'row__'.$landingPage['id'];
            $response['data'][$i]["DT_RowClass"] = 'colore';
            $response['data'][$i]['id'] = '<a data-toggle="tooltip" title="modifica" data-placement="right" href="/blueseal/marketing/landing/modifica?id='.$landingPage['id'].'">'.$landingPage['id'].'</a>';
            $response['data'][$i]['title'] = $landingPage['title'];
            $response['data'][$i]['subtitle'] = $landingPage['subtitle'];
            $response['data'][$i]['creationDate'] = $creationDate->format('d-m-Y H:i');
            $response['data'][$i]['updateDate'] = $updateDate->format('d-m-Y H:i');

            $i++;
        }

        return json_encode($response);
    }
}