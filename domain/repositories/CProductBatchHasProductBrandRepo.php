<?php

namespace bamboo\domain\repositories;

use bamboo\core\db\pandaorm\repositories\ARepo;
use bamboo\core\exceptions\BambooException;
use bamboo\domain\entities\CProductBatch;
use bamboo\domain\entities\CProductBatchHasProductBrand;

/**
 * Class CProductBatchHasProductBrandRepo
 * @package bamboo\domain\repositories
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 29/08/2018
 * @since 1.0
 */
class CProductBatchHasProductBrandRepo extends ARepo
{
    /**
     * @param $productBatchId
     * @param $brandIds
     * @return bool
     */
    public function insertNewProductBrand($productBatchId, $brandIds)
    {
        foreach ($brandIds as $brandId){

            /** @var CProductBatchHasProductBrand $ext */
            $ext = $this->findOneBy(['productBatchId'=>$productBatchId, 'productBrandId'=>$brandId]);

            if(!is_null($ext)) continue;

            /** @var CProductBatchHasProductBrand $phpb */
            $phpb = $this->getEmptyEntity();
            $phpb->productBatchId = $productBatchId;
            $phpb->productBrandId = $brandId;
            $phpb->smartInsert();
        }

        return true;
    }


}