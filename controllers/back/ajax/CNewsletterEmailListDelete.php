<?php

namespace bamboo\controllers\back\ajax;

use bamboo\core\db\pandaorm\repositories\CRepo;
use bamboo\core\exceptions\BambooException;
use bamboo\domain\entities\CNewsletterCampaign;
use bamboo\domain\entities\CNewsletterEmailList;
use bamboo\domain\repositories\CNewsletterCampaignRepo;


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
class CNewsletterEmailLIstDelete extends AAjaxController
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
        /** @var CRepo $newsletterEmailList */
        $newsletterEmailList = \Monkey::app()->repoFactory->create('NewsletterEmailList');

        /** @var CNewsletterEmailList $newsletter */
        $newsletter = $newsletterEmailList->findOneBy(['id'=>$id]);
        $newsletter->delete();
        $res = "Segmento di Pubblico Cancellato";
        return $res;

    }



}