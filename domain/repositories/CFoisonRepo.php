<?php
namespace bamboo\domain\repositories;

use bamboo\core\base\CObjectCollection;
use bamboo\core\db\pandaorm\repositories\ARepo;
use bamboo\core\db\pandaorm\repositories\CRepo;
use bamboo\domain\entities\CFoison;
use bamboo\domain\entities\CFoisonHasInterest;
use bamboo\domain\entities\CRbacRole;

/**
 * Class CFoisonRepo
 * @package bamboo\domain\repositories
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 15/03/2018
 * @since 1.0
 */
class CFoisonRepo extends ARepo
{
    /**
     * @param $name
     * @param $surname
     * @param $email
     * @param $userId
     * @return CFoison
     */
    public function assignUser($name, $surname, $email, $userId) : CFoison{
        $faison = $this->getEmptyEntity();
        $faison->name = $name;
        $faison->surname = $surname;
        $faison->email = $email;
        $faison->userId = $userId;
        $faison->smartInsert();

        /** @var CRepo $uhbrRepo */
        $uhbrRepo = \Monkey::app()->repoFactory->create('UserHasRbacRole');

        $uhbr = $uhbrRepo->getEmptyEntity();
        $uhbr->userId = $userId;
        $uhbr->rbacRoleId = CRbacRole::WORKER;
        $uhbr->assignmentDate = date('Y-m-d H:i:s');
        $uhbr->smartInsert();


        return $faison;

    }

    /**
     * @param $id
     * @return bool
     * @throws \bamboo\core\exceptions\BambooException
     * @throws \bamboo\core\exceptions\BambooORMInvalidEntityException
     * @throws \bamboo\core\exceptions\BambooORMReadOnlyException
     */
    public function checkStatusForEachWorkCategory($id)
    {

        /** @var CFoison $foison */
        $foison = $this->findOneBy(["id"=>$id]);
        /** @var CObjectCollection $allPB */
        $allPB = $foison->getClosedTimeRanchProductBatch();

        /** @var CObjectCollection $interests */
        $interests = $foison->foisonHasInterest;

        //Prendo interessi
        /** @var CFoisonHasInterest $interest */
        foreach ($interests as $interest) {

            //Prendo i lotti relativi a quelli interess
            /** @var CObjectCollection $allPbForCat */
            $allPbForCat = $allPB->findByKey("workCategoryId",$interest->workCategoryId);
            //Se ne sono tre e rispettano lo standard passo la categoria a regualar altrimenti no!
            if(count($allPbForCat) == 3) {
                $rank = $foison->totalRank(false, $allPbForCat);

                if($rank >= 8){
                    $interest->foisonStatusId = 2;
                } else if ($rank >=6 AND $rank < 8) {
                    $interest->foisonStatusId = 3;
                } else {
                    $interest->foisonStatusId = 4;
                }
                $interest->update();
            }
        }

        return true;
    }
}