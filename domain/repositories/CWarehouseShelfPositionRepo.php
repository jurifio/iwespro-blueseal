<?php

namespace bamboo\domain\repositories;

use bamboo\core\db\pandaorm\repositories\ARepo;
use bamboo\core\exceptions\BambooException;
use bamboo\domain\entities\COrderLine;
use bamboo\domain\entities\CShipment;
use bamboo\domain\entities\CWarehouse;
use bamboo\domain\entities\CWarehouseShelf;


/**
 * Class CWharehouseShelfPositionRepo
 * @package bamboo\domain\repositories
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date $date
 * @since 1.0
 */
class CWarehouseShelfPositionRepo extends ARepo
{
    /**
     * @param CShipment $shipment
     * @return array
     * @throws BambooException
     */
    public function placeShipmentInPositions(CShipment $shipment)
    {
        $warehouses = \Monkey::app()->repoFactory->create('Warehouse')->findBy(['addressBookId' => $shipment->toAddress->id]);
        $positions = [];
        foreach ($shipment->orderLine as $orderLine) {
            /** @var COrderLine $orderLine */
            foreach ($orderLine->order->orderLine as $orderLine2) {
                if($orderLine2->warehouseShelfPositionId != null) {
                    $orderLine->warehouseShelfPositionId = $orderLine2->warehouseShelfPositionId;
                    $orderLine->update();
                    $positions[] = $orderLine->warehouseShelfPositionId;
                    continue 2;
                }
            }
            foreach ($warehouses as $warehouse) {
                /** @var CWarehouse $warehouse */
                foreach ($warehouse->warehouseShelf as $warehouseShelf) {
                    /** @var CWarehouseShelf $warehouseShelf */
                    foreach ($warehouseShelf->warehouseShelfPosition as $warehouseShelfPosition) {
                        /** CWarehouseShelfPosition $warehouseShelfPosition */
                        if($warehouseShelfPosition->isEmpty()) {
                            $orderLine->warehouseShelfPositionId = $warehouseShelfPosition->id;
                            $orderLine->update();
                            $positions[] = $orderLine->warehouseShelfPositionId;
                            continue 3;
                        }
                    }
                }
            }
            if($orderLine->warehouseShelfPositionId == null) throw new BambooException('Could not find a position for OrderLine '.$orderLine->printId());
        }

        return $positions;
    }
}