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
 *
 */
class CProductBatch extends AEntity
{
    protected $entityTable = 'ProductBatch';
    protected $primaryKeys = ['id'];

    public function isComplete(){

        $workCategory = $this->contractDetails->workCategory->id;

        switch ($workCategory){
            case CWorkCategory::NORM:
                /** @var CObjectCollection $pBdetails */
                $pBdetails = $this->productBatchDetails;

                /** @var CProductBatchDetails $pBdetail */
                foreach ($pBdetails as $pBdetail){
                    if(!is_null($pBdetail->workCategorySteps->rgt)) return false;
                }
                break;
            case CWorkCategory::BRAND:
                /** @var CObjectCollection $pbhpbs */
                $pbhpbs = $this->productBatchHasProductBrand;

                /** @var CProductBatchHasProductBrand $pbhpb */
                foreach ($pbhpbs as $pbhpb){
                    if(!is_null($pbhpb->workCategorySteps->rgt)) return false;
                }
                break;
        }

        return true;

    }

    public function isValid(){

        $workCategory = $this->contractDetails->workCategory->id;

        $unfitElement = [];

        switch ($workCategory){
            case CWorkCategory::NORM:
                /** @var CObjectCollection $pBdetails */
                $pBdetails = $this->productBatchDetails;

                /** @var CProductBatchDetails $pBdetail */
                foreach ($pBdetails as $pBdetail){

                    if($pBdetail->workCategoryStepsId == CProductBatchDetails::UNFIT_NORM){
                        $unfitElement[] = 'id: '.$pBdetail->id.
                            ' | Lotto: '.$pBdetail->productBatchId.
                            ' | Prodotto: '.$pBdetail->productId.'-'.$pBdetail->productVariantId;
                    }
                }
                break;
            case CWorkCategory::BRAND:
                /** @var CObjectCollection $pbhpbs */
                $pbhpbs = $this->productBatchHasProductBrand;

                /** @var CProductBatchHasProductBrand $pbhpb */
                foreach ($pbhpbs as $pbhpb){

                    if($pbhpb->workCategoryStepsId == CProductBatchHasProductBrand::UNFIT_BRAND){
                        $unfitElement[] =
                            ' | Lotto: '.$pbhpb->productBatchId.
                            ' | Brand: '.$pbhpb->productBrandId;
                    }
                }
                break;
        }



        if(empty($unfitElement)) return 'ok';

        return $unfitElement;
    }
}