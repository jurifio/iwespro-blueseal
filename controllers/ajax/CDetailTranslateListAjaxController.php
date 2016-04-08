<?php
namespace bamboo\blueseal\controllers\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\core\db\pandaorm\entities\IEntity;
use bamboo\core\exceptions\BambooDBALException;
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
        $this->app->setLang(new CLang(1, 'it'));
        $this->urls['base'] = $this->app->baseUrl(false) . "/blueseal/";
        $this->urls['page'] = $this->urls['base'] . "prodotti";
        $this->urls['dummy'] = $this->app->cfg()->fetch('paths', 'dummyUrl');

        $this->em = new \stdClass();
        $this->em->productsDetail = $this->app->entityManagerFactory->create('ProductDetail');

        return $this->{$action}();
    }

    public function get()
    {
        $datatable = new CDataTables('ProductDetailTranslationView', ['id'], $this->app->router->request()->getRequestData());
        $modifica = $this->urls['base'] . "traduzioni/dettagli/modifica";

	    if($this->app->router->request()->getRequestData('useTargetLang')) {
		    $langs = [];
		    foreach($this->app->repoFactory->create('Lang')->findAll() as $lang) {
			    if($this->app->router->request()->getRequestData('useTargetLang') == $lang->id) {
				    $langs[] = 'x';
			    } else {
				    $langs[] = '_';
			    }
		    }
		    $langs[0] = 1;
		    $datatable->addLikeCondition('translatedLangId',implode('|',$langs));
	    }

        $userHasPermission = $this->app->getUser()->hasPermission('/admin/product/edit');

        if (!empty($this->authorizedShops)) {
            $datatable->addCondition('shopId', $this->authorizedShops);
        }

        $productDetails = $this->app->repoFactory->create('ProductDetailTranslationView')->em()->findBySql($datatable->getQuery(), $datatable->getParams());
        $count = $this->em->productsDetail->findCountBySql($datatable->getQuery(true), $datatable->getParams());
        $totalCount = $this->em->productsDetail->findCountBySql($datatable->getQuery(true), $datatable->getParams());

        $repo = $this->app->repoFactory->create('Lang');
        $activeLanguages = $repo->findBy(['isActive' => true]);

        $response = [];
        $response ['draw'] = $this->app->router->request()->getRequestData('draw');
        $response ['recordsTotal'] = $totalCount;
        $response ['recordsFiltered'] = $count;
        $response ['data'] = [];

        $i = 0;

        foreach ($productDetails as $productDetail)
        {
            $html = '';

            $translationStatus = array_combine(explode('|', $productDetail->translatedLangId), explode('|', $productDetail->translatedName));

            foreach ($activeLanguages as $activeLanguage) {
                if (in_array($activeLanguage->id, explode('|', $productDetail->translatedLangId))) {
                    $html .= '<span class="badge badge-green" data-toggle="tooltip" title="' . $translationStatus[$activeLanguage->id] . '" data-placement="left">' . $activeLanguage->lang . '</span>';
                } else {
                    $html .= '<span class="badge badge-red">' . $activeLanguage->lang . '</span>';
                }
            }

            $response['data'][$i]["DT_RowId"] = 'row__' . $productDetail->id;
            $response['data'][$i]["DT_RowClass"] = 'colore';
            $response['data'][$i]['id'] = $userHasPermission ? '<a data-toggle="tooltip" title="modifica" data-placement="right" href="' . $modifica . '?id=' . $productDetail->id . '">' . $productDetail->id . '</a>' : $productDetail->id;
            $response['data'][$i]['source'] = $translationStatus[1];

            if ($this->app->router->request()->getRequestData('useTargetLang')) {
                $input = '<div class="form-group form-group-default" style="width:604px">';
                $input .= '<input type="text" class="form-control dt-input" style="width: 580px"
                    id="detailId_' . $productDetail->id . '"
                    name="detailId_' . $productDetail->id . '"
                    data-lang = "'.$this->app->router->request()->getRequestData('useTargetLang').'"
                    value="' . (isset($translationStatus[$this->app->router->request()->getRequestData('useTargetLang')]) ? $translationStatus[$this->app->router->request()->getRequestData('useTargetLang')] : null) . '"/>';
                $input .= '</div>';

                $response['data'][$i]['target'] = $input;
            } else {
                $response['data'][$i]['target'] = $translationStatus[1];
            }

            $response['data'][$i]['status'] = $html;

            $i++;
        }

        return json_encode($response);
    }

    public function put()
    {
        $langId = $this->app->router->request()->getRequestData('lang');
        $detailName = $this->app->router->request()->getRequestData('name');
        $detailId = $this->app->router->request()->getRequestData('id');

        $detailRepo = $this->app->repoFactory->create('ProductDetailTranslation');
        $entity = $detailRepo->findOneBy(['langId'=>$langId, 'productDetailId'=>$detailId]);

        try {
            if (!$entity instanceof IEntity) {
                $entity = $detailRepo->getEmptyEntity();
                $entity->productDetailId = $detailId;
                $entity->langId = $langId;
                $entity->name = $detailName;
                $entity->insert();
             }
        } catch (BambooDBALException $e) {
        } catch (\Exception $e) {
            $this->app->router->response()->raiseProcessingError()->sendHeaders();
            return;
        }

        try {
            $entity->name = $detailName;
            $entity->update();
        } catch (BambooDBALException $e) {
            $this->app->router->response()->raiseProcessingError()->sendHeaders();
            return;
        }
    }
}