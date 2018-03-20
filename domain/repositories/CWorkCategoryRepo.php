<?php
namespace bamboo\domain\repositories;

use bamboo\core\db\pandaorm\repositories\ARepo;
use bamboo\domain\entities\CProductBatch;
/**
 * Class CWorkCategoryRepo
 * @package bamboo\domain\repositories
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 20/03/2018
 * @since 1.0
 */
class CWorkCategoryRepo extends ARepo
{
    /**
     * @param $id
     * @return \bamboo\domain\entities\CWorkCategory
     */
    public function getCategoryFromProductBatch($id)
    {
        /** @var CProductBatch $productBatch */
        $productBatch = \Monkey::app()->repoFactory->create('ProductBatch')->findOneBy(['id'=> $id]);

        return $productBatch->contractDetails->workCategory;

    }
}