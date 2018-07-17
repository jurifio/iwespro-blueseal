<?php

namespace bamboo\controllers\back\ajax;

use bamboo\core\base\CObjectCollection;
use bamboo\core\db\pandaorm\repositories\CRepo;
use bamboo\core\exceptions\BambooException;
use bamboo\domain\entities\CCartAbandonedEmailParam;
use bamboo\domain\entities\CCartAbandonedEmailSend;
use bamboo\domain\entities\CCouponType;
use bamboo\domain\repositories\CNewsletterUserRepo;


/**
 * Class CCartAbandonedEmailParamDelete
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 17/07/2018
 * @since 1.0
 */
class CCartAbandonedEmailParamDelete extends AAjaxController
{



    /**
     * @return mixed
     * @throws BambooException
     * @throws \bamboo\core\exceptions\BambooORMInvalidEntityException
     * @throws \bamboo\core\exceptions\BambooORMReadOnlyException
     */

    public function put(){
        $data  = $this->app->router->request()->getRequestData();
        $id = $data["id"];
        $value = $id;
        /** @var CRepo $cartAbandonedEmailParamRepo */
        $cartAbandonedEmailParamRepo = \Monkey::app()->repoFactory->create('CartAbandonedEmailParam');

        /** @var CCartAbandonedEmailParam $cartAbandonedEmailParam */
        $cartAbandonedEmailParam = $cartAbandonedEmailParamRepo->findOneBy(['id'=>$value]);
        $couponTypeId=$cartAbandonedEmailParam->couponTypeId;

        $cartAbandonedEmailParam->delete();
        /** @var $cartAbandonedEmailSend CObjectCollection */
        $cartAbandonedEmailSend=\Monkey::app()->repoFactory->create('CartAbandonedEmailSend')->findBy(['couponTypeId'=>$couponTypeId]);
        foreach ($cartAbandonedEmailSend as $cartAbandonedEmailSends){
            $cartAbandonedEmailSends->delete();

        }
        $res = " Regola Cancellata";


        return $res;

    }



}