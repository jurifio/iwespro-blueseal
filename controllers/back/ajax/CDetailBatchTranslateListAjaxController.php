<?php

namespace bamboo\controllers\back\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\core\db\pandaorm\entities\IEntity;
use bamboo\core\db\pandaorm\repositories\CRepo;
use bamboo\core\exceptions\BambooDBALException;
use bamboo\core\intl\CLang;
use bamboo\domain\entities\CProductBatchHasProductDetail;
use bamboo\domain\repositories\CProductBatchHasProductDetailRepo;
use bamboo\domain\repositories\CProductDetailRepo;


/**
 * Class CDetailBatchTranslateListAjaxController
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 08/01/2019
 * @since 1.0
 */
class CDetailBatchTranslateListAjaxController extends AAjaxController
{
    public function get()
    {
        $pb = $this->data['pb'];

        /** @var CProductDetailRepo $pDetR */
        $pDetR = \Monkey::app()->repoFactory->create('ProductDetail');
        $productDetail = $pDetR->getEmptyEntity();
        
        $langId = $this->data['lang'];
        $sql = "SELECT
                  `view`.`id`                                     AS `id`,
                  `view`.`slug`                                   AS `slug`,
                  `view`.`translatedLangId`                       AS `translatedLangId`,
                  `view`.`translatedName`                         AS `translatedName`,
                  if((sum(`ProductSku`.`stockQty`) > 0), 1, 0)    AS `hasQuantity`,
                  count(DISTINCT `ProductSku`.`productVariantId`) AS `timesDetailIsUsedInProduct`,
                  `view`.workCategoryStepsId                      AS  `workCategoryStepsId`
                FROM ((
                 (
                 SELECT
                    pd.id,
                    pd.slug,
                    group_concat(l.lang ORDER BY l.id ASC SEPARATOR '|')                                                 AS translatedLangCode,
                    group_concat(if(pdt.name IS NULL OR pdt.name = '', 'x', pdt.langId) ORDER BY l.id ASC SEPARATOR
                                 '|')                                                                                    AS translatedLangId,
                    group_concat(if(pdt.name IS NULL OR pdt.name = '', 'x', pdt.name) ORDER BY l.id ASC SEPARATOR
                                 '|')                                                                                    AS translatedName,
                    pbhpd.workCategoryStepsId                                                                            AS workCategoryStepsId 
                  FROM ProductDetail pd LEFT JOIN Lang l ON 1 = 1
                    LEFT JOIN
                    ProductDetailTranslation pdt ON pd.id = pdt.productDetailId AND pdt.langId = l.id
                    JOIN ProductBatchHasProductDetail pbhpd ON pd.id = pbhpd.productDetailId
                    WHERE pbhpd.productBatchId = $pb AND pbhpd.langId = $langId
                  GROUP BY pd.id
                 )
                 `view`
                  JOIN `ProductSheetActual` ON ((`ProductSheetActual`.`productDetailId` = `view`.`id`))) JOIN `ProductSku`
                    ON (((`ProductSheetActual`.`productId` = `ProductSku`.`productId`) AND
                         (`ProductSheetActual`.`productVariantId` = `ProductSku`.`productVariantId`))))
                GROUP BY `view`.`id`";
        $datatable = new CDataTables($sql, $productDetail->getPrimaryKeys(), $this->app->router->request()->getRequestData(), true);


        $langs = [];
        foreach (\Monkey::app()->repoFactory->create('Lang')->findAll() as $lang) {
            if ($langId == $lang->id) {
                $langs[] = 'x';
            } else {
                $langs[] = '_';
            }
        }
        $langs[0] = 1;


        $query = $datatable->getQuery(false, true);
        $productDetails = $this->app->dbAdapter->query($query, $datatable->getParams())->fetchAll();
        $count = \Monkey::app()->repoFactory->create('ProductDetail')->em()->findCountBySql($datatable->getQuery(true), $datatable->getParams());
        $totalCount = \Monkey::app()->repoFactory->create('ProductDetail')->em()->findCountBySql($datatable->getQuery(true), $datatable->getParams());

        $activeLanguages = \Monkey::app()->repoFactory->create('Lang')->findBy(['isActive' => true]);

        $response = [];
        $response ['draw'] = $this->app->router->request()->getRequestData('draw');
        $response ['recordsTotal'] = $totalCount;
        $response ['recordsFiltered'] = $count;
        $response ['data'] = [];

        /** @var CProductBatchHasProductDetailRepo $pBhPdRepo */
        $pBhPdRepo = \Monkey::app()->repoFactory->create('ProductBatchHasProductDetail');

        foreach ($productDetails as $productDetail) {

            /** @var CProductBatchHasProductDetail $pBHpD */
            $pBHpD = $pBhPdRepo->findOneBy(['productBatchId'=>$pb, 'productDetailId'=>$productDetail['id'], 'langId' => $langId]);

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
            $row['id'] = $productDetail['id'];
            $row['source'] = $translationStatus[1];


            $input = '<div class="form-group form-group-default" style="width:604px">';
            $input .= '<input type="text" class="form-control dt-input" style="width: 580px"
                id="detailId_' . $productDetail['id'] . '"
                name="detailId_' . $productDetail['id'] . '"
                data-lang = "' . $langId . '"
                value="' .  (isset($translationStatus[$this->data['lang']]) ? $translationStatus[$this->data['lang']] : null) . '"/>';
            $input .= '</div>';

                $row['target'] = $input;


            if (($pBHpD->workCategoryStepsId == CProductBatchHasProductDetail::UNFIT_PRODUCT_DETAIL_ENG
                        || $pBHpD->workCategoryStepsId == CProductBatchHasProductDetail::UNFIT_PRODUCT_DETAIL_DTC) && $pBHpD->productBatch->unfitDate == 0) {
                    $stepName = '<p style="color: red; font-weight: bold">' . $pBHpD->workCategorySteps->name . ' IN VERIFICA, NON MODIFICARE!</p>';
                } else if ($pBHpD->workCategoryStepsId == CProductBatchHasProductDetail::UNFIT_PRODUCT_DETAIL_ENG
                    || $pBHpD->workCategoryStepsId == CProductBatchHasProductDetail::UNFIT_PRODUCT_DETAIL_DTC) {
                    $stepName = '<p style="color: red; font-weight: bold">' . $pBHpD->workCategorySteps->name . ' DA MODIFICARE</p>';
                } else {
                    $stepName = $pBHpD->workCategorySteps->name;
                }

            $row['workCategoryStepsId'] = $stepName;
            $row['work_category'] = $pBHpD->productBatch->workCategoryId;
            $response ['data'][] = $row;
        }

        return json_encode($response);
    }

    public function put()
    {
        $langId = $this->app->router->request()->getRequestData('lang');
        $detailName = $this->app->router->request()->getRequestData('name');
        $detailId = $this->app->router->request()->getRequestData('id');

        $detailRepo = \Monkey::app()->repoFactory->create('ProductDetailTranslation');
        $entity = $detailRepo->findOneBy(['langId' => $langId, 'productDetailId' => $detailId]);

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