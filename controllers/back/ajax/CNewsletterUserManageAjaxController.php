<?php

namespace bamboo\controllers\back\ajax;
use bamboo\core\db\pandaorm\repositories\CRepo;
use bamboo\domain\entities\CNewsletterUser;


/**
 * Class CNewsletterUserManageAjaxController
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 19/06/2018
 * @since 1.0
 */
class CNewsletterUserManageAjaxController extends AAjaxController
{
    /**
     * @return string
     * @throws \bamboo\core\exceptions\BambooException
     * @throws \bamboo\core\exceptions\BambooORMInvalidEntityException
     * @throws \bamboo\core\exceptions\BambooORMReadOnlyException
     */
    public function post()
    {

        $ids = \Monkey::app()->router->request()->getRequestData('ids');
        $sex = \Monkey::app()->router->request()->getRequestData('sex');

        /** @var CRepo $nUR */
        $nUR = \Monkey::app()->repoFactory->create('NewsletterUser');

        foreach ($ids as $id){

            /** @var CNewsletterUser $newsUser */
            $newsUser = $nUR->findOneBy(['id'=>$id]);
            $newsUser->genderNewsletterUser = $sex;
            $newsUser->update();
        }

        return "Sesso aggiornato con successo";

    }
}