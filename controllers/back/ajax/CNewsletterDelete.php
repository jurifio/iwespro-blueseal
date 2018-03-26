<?php

namespace bamboo\controllers\back\ajax;

use bamboo\core\db\pandaorm\repositories\CRepo;
use bamboo\core\exceptions\BambooException;
use bamboo\domain\entities\CNewsletter;
use bamboo\domain\repositories\CNewsletterUserRepo;


/**
 * Class CProductSizeGroupManage
 * @package bamboo\controllers\back\ajax
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
class CNewsletterDelete extends AAjaxController
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
        $value = strstr($value, ">");
        $value = strstr($value, "</a>", true);
        $value =trim(str_replace(">","",$value));
        /** @var CRepo $newsletterUserRepo */
        $newsletterUserRepo = \Monkey::app()->repoFactory->create('Newsletter');

        /** @var CNewsletter $newsletter */
        $newsletter = $newsletterUserRepo->findOneBy(['id'=>$value]);
        $newsletter->delete();
        $res = " Newsletter Cancellata";
        return $res;

    }



}