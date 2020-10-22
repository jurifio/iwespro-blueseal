<?php

namespace bamboo\domain\entities;

use bamboo\core\base\CObjectCollection;
use bamboo\core\db\pandaorm\entities\AEntity;
use bamboo\core\exceptions\BambooException;
use bamboo\domain\repositories\CWorkCategoryRepo;
use bamboo\domain\repositories\CWorkCategoryStepsRepo;

/**
 * Class CProductBatch
 * @package bamboo\domain\entities
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 22/08/2018
 * @since 1.0
 *
 *
 * @property CContractDetails $contractDetails
 * @property CWorkCategory $workCategory
 * @property CObjectCollection $productBatchHasProductName
 * @property CObjectCollection $productBatchHasProductBrand
 * @property CObjectCollection $productBatchTextManage
 * @property CObjectCollection $productBatchDetails
 * @property CObjectCollection $productBatchHasProductDetail
 *
 *
 */
class CProductBatch extends AEntity
{
    protected $entityTable = 'ProductBatch';
    protected $primaryKeys = ['id'];

    public function isComplete()
    {

        $elems = $this->getElements();

        foreach ($elems as $elem) {
            if (!is_null($elem->workCategorySteps->rgt)) return false;
        }

        return true;

    }

    public function isValid()
    {

        $unfitElement = [];
        $elems = $this->getElements();

        /** @var CWorkCategoryStepsRepo $wksR */
        $wksR = \Monkey::app()->repoFactory->create('WorkCategorySteps');

        $countNotNormalizedElems = 0;

        foreach ($elems as $elem) {
            /** fixme: vedere se elem è una istanza di textmanage */
            switch ($elem->workCategoryStepsId) {
                case CProductBatchDetails::UNFIT_NORM:
                    $unfitElement[] = 'id: ' . $elem->id .
                        ' | Lotto: ' . $elem->productBatchId .
                        ' | Prodotto: ' . $elem->productId . '-' . $elem->productVariantId;
                    break;
                case CProductBatchHasProductBrand::UNFIT_BRAND:
                    $unfitElement[] =
                        ' | Lotto: ' . $elem->productBatchId .
                        ' | Brand: ' . $elem->productBrandId;
                    break;
                case CProductBatchHasProductName::UNFIT_PRODUCT_NAME_ENG:
                case CProductBatchHasProductName::UNFIT_PRODUCT_NAME_DTC:
                case CProductBatchHasProductName::UNFIT_PRODUCT_NAME_RUS:
                case CProductBatchHasProductName::UNFIT_PRODUCT_NAME_CIN:
                case CProductBatchHasProductName::UNFIT_PRODUCT_NAME_FRE:
                    $unfitElement[] =
                        ' | Lotto: ' . $elem->productBatchId .
                        ' | Name: ' . $elem->productName;
                    break;
                case ($elem instanceof CProductBatchTextManage && $elem->getUnfitStep()):
                    $unfitElement[] =
                        ' | Lotto: ' . $elem->productBatchId;
                    break;
                case CProductBatchHasProductDetail::UNFIT_PRODUCT_DETAIL_ENG:
                case CProductBatchHasProductDetail::UNFIT_PRODUCT_DETAIL_DTC:
                case CProductBatchHasProductDetail::UNFIT_PRODUCT_DETAIL_RUS:
                case CProductBatchHasProductDetail::UNFIT_PRODUCT_DETAIL_FRE:
                case CProductBatchHasProductDetail::UNFIT_PRODUCT_DETAIL_CIN:
                    $unfitElement[] =
                        ' | Lotto: ' . $elem->productBatchId .
                        ' | Dettaglio: ' . $elem->productDetailId;
                    break;
            }

            if ($wksR->getFirstStepsFromCategoryId($elem->productBatch->workCategoryId)->id == $elem->workCategoryStepsId)
                $countNotNormalizedElems++;
        }

        $unfitElement[] = ' Elementi non normalizzati: ' . $countNotNormalizedElems;

        if (empty($unfitElement)) return 'ok';

        return $unfitElement;
    }

    public function getElements()
    {

        $elems = null;
        $workCategory = null;

        if (is_null($this->contractDetailsId)) {
            $workCategory = $this->workCategoryId;
        } else {
            $workCategory = $this->contractDetails->workCategory->id;
        }

        switch ($workCategory) {
            case CWorkCategory::NORM:
                $elems = $this->productBatchDetails;
                break;
            case CWorkCategory::BRAND:
                $elems = $this->productBatchHasProductBrand;
                break;
            case CWorkCategory::NAME_ENG:
            case CWorkCategory::NAME_DTC:
                $elems = $this->productBatchHasProductName;
                break;
            case CWorkCategory::TXT_FAS:
            case CWorkCategory::TXT_FAS_BLOG:
            case CWorkCategory::TXT_INFL:
            case CWorkCategory::TXT_PRT:
            case CWorkCategory::TXT_BRAND:
            case CWorkCategory::TXT_FB:
            case CWorkCategory::TXT_COPY_BLOG_POST:
            case CWorkCategory::TXT_COPY_BRAND:
            case CWorkCategory::TXT_FB_CR:
            case CWorkCategory::TXT_FB_VID:
            case CWorkCategory::TXT_IN_PHOTO_FEED:
            case CWorkCategory::TXT_IN_PHOTO_STORY:
            case CWorkCategory::TXT_IN_VIDEO_FEED:
            case CWorkCategory::TXT_IN_VIDEO_STORY:
                $elems = new CObjectCollection();
                $productBatchTextManageColl = $this->productBatchTextManage;
                if(!$productBatchTextManageColl->isEmpty()){
                    /** @var CProductBatchTextManage $productBatchTextManage */
                    foreach ($productBatchTextManageColl as $productBatchTextManage){
                        $elems->add($productBatchTextManage);
                    }
                }
                break;
            case CWorkCategory::DET_ENG:
            case CWorkCategory::DET_DTC:
            case CWorkCategory::DET_RUS:
            case CWorkCategory::DET_CHI:
            case CWorkCategory::DET_FRE:

                $elems = $this->productBatchHasProductDetail;
                break;
        }

        return $elems;
    }

    /**
     * @return array
     */
    public function getNormalizedElements()
    {
        $nElem = [];
        $elems = $this->getElements();

        foreach ($elems as $elem) {
            if (is_null($elem->workCategorySteps->rgt)) $nElem[] = $elem;
        }

        return $nElem;
    }


    /**
     * @return array
     */
    public function getNotNormalizedElements()
    {
        $nElem = [];
        $elems = $this->getElements();

        foreach ($elems as $elem) {
            if (!is_null($elem->workCategorySteps->rgt)) $nElem[] = $elem;
        }

        return $nElem;
    }

    /**
     * @param CUser $user
     * @return mixed
     */
    public function getContractDetailFromUnassignedProductBatch(CUser $user)
    {
        $i = 0;
        $contractDetails = null;
        /** @var CObjectCollection $contracts */
        $contracts = $user->foison->getContract();
        /** @var CContracts $contract */
        foreach ($contracts as $contract) {
            $contractDetails = $contract->contractDetails->findOneByKeys(["workCategoryId" => $this->workCategoryId]);

            if(!is_null($contractDetails)) $i++;
        }

        if($i > 1) return false;
        return $contractDetails;

    }
}