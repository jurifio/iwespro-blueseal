<?php
namespace bamboo\domain\repositories;

use bamboo\core\db\pandaorm\repositories\ARepo;
use bamboo\domain\entities\CShootingBooking;
use bamboo\domain\entities\CUser;

/**
 * Class CShootingBookingRepo
 * @package bamboo\domain\repositories
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 04/04/2018
 * @since 1.0
 */
class CShootingBookingRepo extends ARepo
{

    public function insertNewShootingBooking($date, $shopId, $cat){

        /** @var CShootingBooking $sb */
        $sb = $this->getEmptyEntity();
        $sb->bookingDate = $date;
        $sb->shopId = $shopId;
        $sb->smartInsert();

        /** @var CShootingBookingHasProductTypeRepo $sbhptRepo */
        $sbhptRepo = \Monkey::app()->repoFactory->create('ShootingBookingHasProductType');

        $res = $sbhptRepo->insertNewShootingBookingHasProductType($sb->id, $cat);

        if($res) return $sb->id;

    }

}