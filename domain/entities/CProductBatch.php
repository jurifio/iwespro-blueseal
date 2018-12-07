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
 * @property CProductBatchTextManage $productBatchTextManage
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
                    $unfitElement[] =
                        ' | Lotto: ' . $elem->productBatchId .
                        ' | Name: ' . $elem->productName;
                    break;
                case $elem->getUnfitStep():
                    $unfitElement[] =
                        ' | Lotto: ' . $elem->productBatchId;
                    break;
            }

            if ($wksR->getFirstStepsFromCategoryId($elem->productBatch->workCategoryId)->id == $elem->workCategoryStepsId) {

                $unfitElement[] = 'Elemento non normalizzato --> id: ' . $elem->id .
                    ' | Lotto: ' . $elem->productBatchId;
            }
        }

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
                $elems = new CObjectCollection();
                $productBatchTextManage = $this->productBatchTextManage;
                if(!is_null($productBatchTextManage)){
                    $elems->add($productBatchTextManage);
                }
                break;
        }

        return $elems;
    }

    public function getNormalizedElements()
    {

        $elems = null;
        $workCategory = null;
        $nElem = [];

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
                $elems = new CObjectCollection();
                $elems->add($this->productBatchTextManage);
                break;
        }

        foreach ($elems as $elem) {
            if (is_null($elem->workCategorySteps->rgt)) $nElem[] = $elem;
        }

        return $nElem;
    }

    public function getNotNormalizedElements()
    {

        $elems = null;
        $workCategory = null;
        $nElem = [];

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
                $elems = new CObjectCollection();
                $elems->add($this->productBatchTextManage);
                break;
        }

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