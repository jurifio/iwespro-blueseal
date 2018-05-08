<?php

namespace bamboo\controllers\back\ajax;

use bamboo\core\db\pandaorm\repositories\CRepo;
use bamboo\core\exceptions\BambooException;
use bamboo\domain\entities\CFoison;
use bamboo\domain\entities\CProductSizeGroup;
use bamboo\domain\entities\CProductSizeMacroGroup;
use bamboo\domain\entities\CUser;
use bamboo\domain\repositories\CFoisonRepo;
use bamboo\domain\repositories\CProductSizeGroupRepo;
use bamboo\domain\repositories\CProductSizeRepo;
use bamboo\domain\repositories\CUserAddressRepo;
use bamboo\domain\repositories\CUserRepo;


/**
 * Class CFoisonDetailManage
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 08/05/2018
 * @since 1.0
 */
class CFoisonDetailManage extends AAjaxController
{
    /**
     * @throws BambooException
     * @throws \bamboo\core\exceptions\BambooORMInvalidEntityException
     * @throws \bamboo\core\exceptions\BambooORMReadOnlyException
     */
    public function post()
    {
        $data = \Monkey::app()->router->request()->getRequestData();

        $user = \Monkey::app()->getUser();
        $userId = $user->id;

        $name = $data["name"];
        $surname = $data["surname"];
        $fiscalCode = $data["fiscalCode"];
        $iban = $data["iban"];
        $address = $data["address"];
        $province = $data["province"];
        $city = $data["city"];
        $postalCode = $data["postalCode"];
        $country = $data["country"];
        $phone = $data["phone"];

        /** @var CUserAddressRepo $userAddressRepo */
        $userAddressRepo = \Monkey::app()->repoFactory->create('UserAddress');

        $userAddressRepo->insertUserAddressFromFoison($userId, $name, $surname, $address, $province, $city, $postalCode, $country, $phone, $iban, $fiscalCode);

        return true;
    }


}