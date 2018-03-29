<?php
namespace bamboo\domain\repositories;

use bamboo\core\db\pandaorm\repositories\ARepo;
use bamboo\domain\entities\CSectional;

/**
 * Class CSectionalRepo
 * @package bamboo\domain\repositories
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 16/03/2018
 * @since 1.0
 */
class CSectionalRepo extends ARepo
{

    /**
     * @param $code
     * @return string
     * @throws \bamboo\core\exceptions\BambooException
     * @throws \bamboo\core\exceptions\BambooORMInvalidEntityException
     * @throws \bamboo\core\exceptions\BambooORMReadOnlyException
     */
    public function createNewSectionalCode($code){

        /** @var CSectional $sectional */
        $sectional = $this->findOneBy(["code"=>$code]);

        if(is_null($sectional->last)){
            $newNumber = $sectional->num;
            $sectional->last = $newNumber;
        } else {
            $newNumber = $sectional->last + 1;
            $sectional->last = $newNumber;
        }

        $sectional->update();

        $newCode = $newNumber.'/'.$sectional->code;

        return $newCode;
    }

    public function calculateNextSectionalNumber($code){
        /** @var CSectional $sectional */
        $sectional = $this->findOneBy(["code"=>$code]);

        if(is_null($sectional->last)){
            $newNumber = $sectional->num;
        } else {
            $newNumber = $sectional->last + 1;
        }

        $newCode = $newNumber.'/'.$sectional->code;

        return $newCode;
    }

    /**
     * @param $shopId
     * @param $typeId
     * @return string
     * @throws \bamboo\core\exceptions\BambooException
     * @throws \bamboo\core\exceptions\BambooORMInvalidEntityException
     * @throws \bamboo\core\exceptions\BambooORMReadOnlyException
     */
    public function createNewSectionalCodeFromShop($shopId, $typeId){

        /** @var CSectional $sectional */
        $sectional = $this->findOneBy(["shopId"=>$shopId, "typeId"=>$typeId]);

        if(is_null($sectional->last)){
            $newNumber = $sectional->num;
            $sectional->last = $newNumber;
        } else {
            $newNumber = $sectional->last + 1;
            $sectional->last = $newNumber;
        }

        $sectional->update();

        $newCode = $shopId.'/'.$newNumber.'/'.$sectional->code.'/'.$typeId;

        return $newCode;
    }


    public function calculateNewSectionalCodeFromShop($shopId, $typeId){

        /** @var CSectional $sectional */
        $sectional = $this->findOneBy(["shopId"=>$shopId, "typeId"=>$typeId]);

        if(is_null($sectional)){
            return true;
        }

        if(is_null($sectional->last)){
            $newNumber = $sectional->num;
        } else {
            $newNumber = $sectional->last + 1;
        }

        $newCode = $sectional->shopId.'/'.$newNumber.'/'.$sectional->code.'/'.$sectional->typeId;

        return $newCode;
    }
}


