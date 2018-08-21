<?php
namespace bamboo\domain\repositories;

use bamboo\core\db\pandaorm\repositories\ARepo;
use bamboo\domain\entities\CProductBatch;
use bamboo\domain\entities\CWorkPriceList;

/**
 * Class CWorkPriceListRepo
 * @package bamboo\domain\repositories
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 21/08/2018
 * @since 1.0
 */
class CWorkPriceListRepo extends ARepo
{
    /**
     * @param array $wcps
     * @param $cat
     * @param int $isActive
     * @return array
     */
    public function insertNewPrice(array $wcps, $cat, $isActive = 1) : array
    {
        $ids = [];
        foreach ($wcps as $wcp) {
            /** @var CWorkPriceList $newWcp */
            $newWcp = $this->getEmptyEntity();
            $newWcp->name = $wcp['name'];
            $newWcp->price = $wcp['price'];
            $newWcp->start_date = $wcp['start'];
            $newWcp->end_date = $wcp['end'];
            $newWcp->active = $isActive;
            $newWcp->workCategoryId = $cat;
            $newWcp->smartInsert();

            $ids[] = $newWcp->id;
        }

        return $ids;
    }
}