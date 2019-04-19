<?php
namespace bamboo\blueseal\jobs;

use bamboo\blueseal\marketplace\prestashop\CPrestashopShop;
use bamboo\core\jobs\ACronJob;

/**
 * Class CPrestashopAddNewShop
 * @package bamboo\blueseal\jobs
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 19/04/2019
 * @since 1.0
 */
class CPrestashopAddNewShop extends ACronJob
{
      /**
     * @param null $args
     */
    public function run($args = null)
    {
        $this->insertNewShopInPrestashop($args);
    }

    /**
     * @param String $shopName
     */
    private function insertNewShopInPrestashop(String $shopName)
    {
        $this->report('Create new shop', 'Init create');

        $prestashopShop = new CPrestashopShop();
        $prestashopShop->addNewShop($shopName);

        $this->report('Create new shop', 'End create');
    }
}