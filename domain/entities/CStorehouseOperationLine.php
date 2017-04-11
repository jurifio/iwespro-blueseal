<?php

namespace bamboo\domain\entities;

use bamboo\core\db\pandaorm\entities\AEntity;
use bamboo\core\exceptions\BambooOutOfBoundException;

/**
 * Class CStorehouseOperationLine
 * @package bamboo\domain\entities
 *
 * @author Bambooshoot Team <emanuele@bambooshoot.agency>
 *
 * @copyright (c) Bambooshoot snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 01/08/2016
 * @since 1.0
 * @property CProductSku $productSku
 * @property CStorehouseOperation $storehouseOperation
 */
class CStorehouseOperationLine extends AEntity
{
    protected $entityTable = 'StorehouseOperationLine';
    protected $primaryKeys = ['storehouseOperationId','shopId', 'storehouseId', 'productId', 'productVariantId','productSizeId'];

    /**
     * @param $int
     * @return mixed|number
     * @throws BambooOutOfBoundException
     */
    public function modifyQty($int) {
        switch ($this->storehouseOperation->storehouseOperationCause->sign){
            case null:
                $this->qty += $int;
                break;
            case 0:
                $this->qty += abs($int);
                break;
            case 1:
                $this->qty -= abs($int);
                break;
            default:
                throw new BambooOutOfBoundException('StorehouseOperationCause Sign not handled: '.$this->storehouseOperation->storehouseOperationCause->sign);
        }
        return $this->qty;
    }
}