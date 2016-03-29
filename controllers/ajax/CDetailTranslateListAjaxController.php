<?php
namespace bamboo\blueseal\controllers\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\core\intl\CLang;


/**
 * Class CDetailTranslateListAjaxController
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
class CDetailTranslateListAjaxController extends AAjaxController
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
        $this->em->productsDetail = $this->app->entityManagerFactory->create('ProductDetail');

        return $this->{$action}();
    }

    public function get()
    {
        $datatable = new CDataTables('vBluesealProductDetailTranslation',['id'],$this->app->router->request()->getRequestData());
        $modifica = $this->urls['base']."traduzioni/dettagli/modifica";

        $userHasPermission = $this->app->getUser()->hasPermission('/admin/product/edit');

        if (!empty($this->authorizedShops)) {
            $datatable->addCondition('shopId',$this->authorizedShops);
        }

        $productDetails = $this->app->repoFactory->create('ProductDetail')->em()->findBySql($datatable->getQuery(),$datatable->getParams());
        $count = $this->em->productsDetail->findCountBySql($datatable->getQuery(true), $datatable->getParams());
        $totalCount = $this->em->productsDetail->findCountBySql($datatable->getQuery(true), $datatable->getParams());

        $repo = $this->app->repoFactory->create('Lang');
        $activeLanguages = $repo->findBy(['isActive'=>true]);

        $response = [];
        $response ['draw'] = $_GET['draw'];
        $response ['recordsTotal'] = $totalCount;
        $response ['recordsFiltered'] = $count;
        $response ['data'] = [];

        $i = 0;

        foreach($productDetails as $productDetail)
        {
            $html = '';

            foreach ($activeLanguages as $activeLanguage)
            {
                $lang = explode('|',$productDetail->translatedLangId);

                if(!is_null($lang)) {
                    $html .= '<span class="badge">' . $activeLanguage->lang . '</span>';
                } else {
                    $html .= '<span class="badge badge-red">' . $activeLanguage->lang . '</span>';
                }
            }

            $response['data'][$i]["DT_RowId"] = 'row__' . $productDetail->id;
            $response['data'][$i]["DT_RowClass"] = 'colore';
            $response['data'][$i]['name'] = $trans->name;
            $response['data'][$i]['slug'] = $userHasPermission ? '<a data-toggle="tooltip" title="modifica" data-placement="right" href="'. $modifica . '?id=' . $productDetail->id . '">' . $productDetail->slug . '</a>' : $productDetail->slug;
            $response['data'][$i]['id'] = $productDetail->id;
            $response['data'][$i]['lang'] = $html;

            $i++;
        }

        echo json_encode($response);
    }
}