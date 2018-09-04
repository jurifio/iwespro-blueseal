<?php
namespace bamboo\domain\repositories;

use bamboo\core\db\pandaorm\repositories\ARepo;
use bamboo\core\db\pandaorm\repositories\CRepo;
use bamboo\domain\entities\CFoison;
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
        $faison->foisonStatusId = 1;
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
}