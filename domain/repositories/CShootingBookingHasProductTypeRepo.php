<?php
namespace bamboo\domain\repositories;

use bamboo\core\base\CObjectCollection;
use bamboo\core\db\pandaorm\repositories\ARepo;
use bamboo\domain\entities\CShooting;
use bamboo\domain\entities\CShootingBooking;
use bamboo\domain\entities\CShootingBookingHasProductType;
use bamboo\domain\entities\CUser;

/**
 * Class CShootingBookingHasProductTypeRepo
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
class CShootingBookingHasProductTypeRepo extends ARepo
{

    public function insertNewShootingBookingHasProductType(int $shootingBookingId, array $cat){

        foreach ($cat as $c){
            /** @var CShootingBookingHasProductType $sbhpt */
            $sbhpt = $this->getEmptyEntity();
            $sbhpt->shootingBookingId = $shootingBookingId;
            $sbhpt->shootingProductTypeId = $c["key"];
            $sbhpt->qty = $c["value"];
            $sbhpt->smartInsert();
        }

        return true;
    }

    /**
     * @param CShootingBooking $shootingBooking
     * @return bool
     */
    public function deleteShootingBookingHasProductType(CShootingBooking $shootingBooking) : bool {

        /** @var CObjectCollection $sbhptColl */
        $sbhptColl = $this->findBy(['shootingBookingId'=>$shootingBooking->id]);

        if(!$sbhptColl->isEmpty()){
            /** @var CShootingBookingHasProductType $sbhpt */
            foreach ($sbhptColl as $sbhpt){
                try {
                    $sbhpt->delete();
                }
                catch (\Throwable $e){
                    return false;
                }
            }
        }

        return true;

    }

}