<?php

namespace bamboo\domain\entities;

use bamboo\core\base\CObjectCollection;
use bamboo\core\db\pandaorm\entities\AEntity;

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
 * @date 15/03/2018
 * @since 1.0
 *
 * @property CContractDetails $contractDetails
 * @property CObjectCollection $productBatchDetails
 * @property CDocument $document
 * @property CObjectCollection $productBrand
 * @property CObjectCollection $productBatchHasProductBrand
 * @property CObjectCollection $productBatchHasProductName
 * @property CWorkPriceList $workPriceList
 *
 */
class CProductBatch extends AEntity
{
    protected $entityTable = 'ProductBatch';
    protected $primaryKeys = ['id'];

    public function isComplete(){

        $workCategory = $this->contractDetails->workCategory->id;


        $elems = $this->getElements();

        foreach ($elems as $elem) {
            if(!is_null($elem->workCategorySteps->rgt)) return false;
        }

        return true;

    }

    public function isValid(){

        $unfitElement = [];

        $elems = $this->getElements();

        foreach ($elems as $elem){

            switch ($elem->workCategoryStepsId){
                case CProductBatchDetails::UNFIT_NORM:
                    $unfitElement[] = 'id: '.$elem->id.
                        ' | Lotto: '.$elem->productBatchId.
                        ' | Prodotto: '.$elem->productId.'-'.$elem->productVariantId;
                    break;
                case CProductBatchHasProductBrand::UNFIT_BRAND:
                    $unfitElement[] =
                        ' | Lotto: '.$elem->productBatchId.
                        ' | Brand: '.$elem->productBrandId;
                    break;
                case CProductBatchHasProductName::UNFIT_PRODUCT_NAME_ENG:
                case CProductBatchHasProductName::UNFIT_PRODUCT_NAME_DTC:
                $unfitElement[] =
                    ' | Lotto: '.$elem->productBatchId.
                    ' | Brand: '.$elem->productName;
                    break;
            }
        }

        if(empty($unfitElement)) return 'ok';

        return $unfitElement;
    }

    public function getElements(){

        $elems = null;
        $workCategory = null;

        if(is_null($this->contractDetailsId)){
            $workCategory = $this->workCategoryId;
        } else {
            $workCategory = $this->contractDetails->workCategory->id;
        }

        switch ($workCategory){
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
        }

        return $elems;
    }
}