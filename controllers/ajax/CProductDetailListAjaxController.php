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
class CProductDetailListAjaxController extends AAjaxController
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
        $datatable = new CDataTables('vBluesealProductDetailList',['id'],$_GET);

        $productsDetail = $this->app->repoFactory->create('ProductDetail')->em()->findBySql($datatable->getQuery(),$datatable->getParams());
        $count = $this->em->productsDetail->findCountBySql($datatable->getQuery(true), $datatable->getParams());
        $totalCount = $this->em->productsDetail->findCountBySql($datatable->getQuery('full'), $datatable->getParams());


        $response = [];
        $response ['draw'] = $_GET['draw'];
        $response ['recordsTotal'] = $totalCount;
        $response ['recordsFiltered'] = $count;
        $response ['data'] = [];

        $i = 0;

        $psaRepo = $this->app->repoFactory->create('ProductSheetModelActual');
        $psmaRepo = $this->app->repoFactory->create('ProductSheetModelActual');

        $colorTemp = '<span style="color: #880611;">{string}</span>';
        foreach($productsDetail as $val){

            //verifico se un dettaglio che non ha prodotti è associato ad un modello

            $psaCount = $psaRepo->findBy(['productDetailId' => $val->id])->count();
            $red = false;

            if (!$psaCount) {
                $psmaCount = $psmaRepo->findBy(['productDetailId' => $val->id])->count();
                if ($psmaCount) $red = true;
            }
			try {
				$response['data'][$i]["DT_RowId"] = 'row__' . $val->id;
				$response['data'][$i]["DT_RowClass"] = 'colore';
				$response['data'][$i]['slug'] = ($red) ? str_replace('{string}', $val->slug, $colorTemp) : $val->slug;
				$response['data'][$i]['name'] = ($red) ? str_replace('{string}', $val->productDetailTranslation->getFirst()->name, $colorTemp) : $val->productDetailTranslation->getFirst()->name;
                $response['data'][$i]['name'] .= " (";
                $dt = $this->app->repoFactory->create('productDetailTranslation')->findBy(['productDetailId' => $val->id]);
                $lang = [];
                foreach ($dt as $vt) {
                    $rLang = $this->app->repoFactory->create('Lang')->findOneBy(['id' => $vt->langId]);
                    $lang[] = $rLang->lang;
                }
                $response['data'][$i]['name'] .= implode(',', $lang);
                $response['data'][$i]['name'] .= ')';
				$i++;
			} catch (\Throwable $e) {
				throw $e;
			}

        }

        return json_encode($response);
    }
}