<?php

namespace bamboo\business\carrier;

use bamboo\core\exceptions\BambooException;
use bamboo\domain\entities\CAddressBook;
use bamboo\domain\entities\CShipment;

/**
 * Interface IImplementedPickUpHandler
 * @package bamboo\business\carrier
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date $date
 * @since 1.0
 */
interface IImplementedPickUpHandler {

    /**
     * @param $fromAddress
     * @param $toAddress
     * @return bool|\DateTime
     * @throws BambooException
     */
    public function getFirstPickUpDate(CAddressBook $fromAddress, CAddressBook $toAddress);

    /**
     * Add a pick-up request
     * @param CShipment $shipment
     * @param $orderId
     * @return CShipment|bool
     */
    public function addPickUp(CShipment $shipment, $orderId);

    /**
     * @param CShipment $shipment
     * @return CShipment|bool
     */
    public function cancelPickUp(CShipment $shipment);
}