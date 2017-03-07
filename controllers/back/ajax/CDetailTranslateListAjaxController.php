<?php
namespace bamboo\controllers\back\ajax;

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
    public function get()
    {
        $productDetail = $this->app->repoFactory->create('ProductDetail')->getEmptyEntity();
        $sql = "SELECT
                  `view`.`id`                                     AS `id`,
                  `view`.`slug`                                   AS `slug`,
                  `view`.`translatedLangId`                       AS `translatedLangId`,
                  `view`.`translatedName`                         AS `translatedName`,
                  if((sum(`ProductSku`.`stockQty`) > 0), 1, 0)    AS `hasQuantity`,
                  count(DISTINCT `ProductSku`.`productVariantId`) AS `timesDetailIsUsedInProduct`
                FROM ((
                 (
                 SELECT
                    pd.id,
                    pd.slug,
                    group_concat(l.lang ORDER BY l.id ASC SEPARATOR '|')                                                 AS translatedLangCode,
                    group_concat(if(pdt.name IS NULL OR pdt.name = '', 'x', pdt.langId) ORDER BY l.id ASC SEPARATOR
                                 '|')                                                                                    AS translatedLangId,
                    group_concat(if(pdt.name IS NULL OR pdt.name = '', 'x', pdt.name) ORDER BY l.id ASC SEPARATOR
                                 '|')                                                                                    AS translatedName
                  FROM ProductDetail pd LEFT JOIN Lang l ON 1 = 1
                    LEFT JOIN
                    ProductDetailTranslation pdt ON pd.id = pdt.productDetailId AND pdt.langId = l.id
                  GROUP BY pd.id
                 )
                 `view`
                  JOIN `ProductSheetActual` ON ((`ProductSheetActual`.`productDetailId` = `view`.`id`))) JOIN `ProductSku`
                    ON (((`ProductSheetActual`.`productId` = `ProductSku`.`productId`) AND
                         (`ProductSheetActual`.`productVariantId` = `ProductSku`.`productVariantId`))))
                GROUP BY `view`.`id`";
        $datatable = new CDataTables($sql, $productDetail->getPrimaryKeys(), $this->app->router->request()->getRequestData(),true);
        $modifica = $this->app->baseUrl(false) . "/blueseal/traduzioni/dettagli/modifica";

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
            $langsCond = implode('|',$langs);
		    $datatable->addIgnobleCondition('translatedLangId', $langsCond);
	    }

        $userHasPermission = $this->app->getUser()->hasPermission('/admin/product/edit');
		$datatable->addCondition('hasQuantity',[1]);

        $query = $datatable->getQuery(false,true);
        $productDetails = $this->app->dbAdapter->query($query, $datatable->getParams())->fetchAll();
        $count = $this->app->repoFactory->create('ProductDetail')->em()->findCountBySql($datatable->getQuery(true), $datatable->getParams());
        $totalCount = $this->app->repoFactory->create('ProductDetail')->em()->findCountBySql($datatable->getQuery(true), $datatable->getParams());

        $activeLanguages = $this->app->repoFactory->create('Lang')->findBy(['isActive' => true]);

        $response = [];
        $response ['draw'] = $this->app->router->request()->getRequestData('draw');
        $response ['recordsTotal'] = $totalCount;
        $response ['recordsFiltered'] = $count;
        $response ['data'] = [];

        foreach ($productDetails as $productDetail)
        {
            $row = [];
            $html = '';
            $translationStatus = array_combine(explode('|', $productDetail['translatedLangId']), explode('|', $productDetail['translatedName']));

            foreach ($activeLanguages as $activeLanguage) {
                if (in_array($activeLanguage->id, explode('|', $productDetail['translatedLangId']))) {
                    $html .= '<span class="badge badge-green" data-toggle="tooltip" title="' . $translationStatus[$activeLanguage->id] . '" data-placement="left">' . $activeLanguage->lang . '</span>';
                } else {
                    $html .= '<span class="badge badge-red">' . $activeLanguage->lang . '</span>';
                }
            }

            $row["DT_RowId"] = 'row__' . $productDetail['id'];
            $row["DT_RowClass"] = 'colore';
            $row['id'] = $userHasPermission ? '<a data-toggle="tooltip" title="modifica" data-placement="right" href="' . $modifica . '?id=' . $productDetail['id'] . '">' . $productDetail['id'] . '</a>' : $productDetail['id'];
            $row['source'] = $translationStatus[1];

            if ($this->app->router->request()->getRequestData('useTargetLang')) {
                $input = '<div class="form-group form-group-default" style="width:604px">';
                $input .= '<input type="text" class="form-control dt-input" style="width: 580px"
                    id="detailId_' . $productDetail['id'] . '"
                    name="detailId_' . $productDetail['id'] . '"
                    data-lang = "'.$this->app->router->request()->getRequestData('useTargetLang').'"
                    value="' . (isset($translationStatus[$this->app->router->request()->getRequestData('useTargetLang')]) ? $translationStatus[$this->app->router->request()->getRequestData('useTargetLang')] : null) . '"/>';
                $input .= '</div>';

                $row['target'] = $input;
            } else {
                $row['target'] = $translationStatus[1];
            }
            $row['status'] = $html;
            $response ['data'][] = $row;
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

        if (!$entity instanceof IEntity) {
            try {
                $entity = $detailRepo->getEmptyEntity();
                $entity->productDetailId = $detailId;
                $entity->langId = $langId;
                $entity->name = $detailName;
                $entity->insert();

            } catch (BambooDBALException $e) {
            } catch (\Throwable $e) {
                $this->app->router->response()->raiseProcessingError()->sendHeaders();
                return;
            }
        } else {
            try {
                $entity->name = $detailName;
                $entity->update();
            } catch (BambooDBALException $e) {
                $this->app->router->response()->raiseProcessingError()->sendHeaders();
                return;
            }
        }
    }
}