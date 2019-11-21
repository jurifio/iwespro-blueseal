<?php

namespace bamboo\controllers\back\ajax;

use bamboo\core\exceptions\RedPandaException;
use bamboo\domain\entities\CUserAddress;
use bamboo\ecommerce\views\VBase;
use bamboo\blueseal\business\CBlueSealPage;
use bamboo\core\theming\CRestrictedAccessWidgetHelper;
use PDO;
use prepare;
use AEntity;
use bamboo\domain\entities\CProductSku;
use bamboo\domain\entities\CSite;
use bamboo\domain\entities\CUserHasShop;
use bamboo\domain\repositories\CUserAddressRepo;
use bamboo\domain\entities\CUser;
use PDOException;


/**
 * Class CImportExternalPickySiteOrder
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 01/06/2019
 * @since 1.0
 */
class CImportExternalPickySiteOrder extends AAjaxController
{
    /**
     * @return bool|string
     * @throws RedPandaException
     * @throws \Exception
     * @throws \bamboo\core\exceptions\BambooException
     */
    public function post()
    {

        $res = "";
        $job = \Monkey::app()->repoFactory->create('Job')->findOneBy(['id' => 119]);
        $job->manualStart=1;
        $job->update();
        $res='Importazione In Esecuzione';

        return $res;


    }


}

