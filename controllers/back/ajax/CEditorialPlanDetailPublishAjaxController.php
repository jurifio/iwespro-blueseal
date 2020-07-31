<?php


namespace bamboo\controllers\back\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\core\db\pandaorm\repositories\CRepo;
use bamboo\domain\entities\CEditorialPlan;
use bamboo\domain\entities\CEditorialPlanArgument;
use bamboo\domain\entities\CEditorialPlanDetail;
use \bamboo\utils\time\STimeToolbox;

/**
 * Class CEditorialPlanDetailPublishAjaxController
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 31/07/2020
 * @since 1.0
 */

class CEditorialPlanDetailPublishAjaxController extends AAjaxController
{



    public function post()
    {
        if(ENV == 'dev') {
            require '/media/sf_sites/vendor/mailgun/vendor/autoload.php';
        }else{
            require '/home/shared/vendor/mailgun/vendor/autoload.php';
        }

        $data = $this->app->router->request()->getRequestData();

        $editorialPlanDetailId = $data['editorialPlanDetailId'];
        /** @var CRepo $editorialPlanDetail */
        $editorialPlanDetail = \Monkey::app()->repoFactory->create('editorialPlanDetail');

        /** @var CEditorialPlanDetail $editorial */
        $editorial = $editorialPlanDetail->findOneBy(['id' => $editorialPlanDetailId]);
        $startEventDate=$editorial->startEventDate;
        $endEventDate=$editorial->endEventDate;
        $isEventVisible=$editorial->isEventVisible;
        $editorialPlanArgumentId=$editorial->editorialPlanArgumentId;
        $isVisibleEditorialPlanArgument=$editorial->isVisibleEditorialPlanArgument;
        $title=$editorial->title;
        $isVisibleDescription=$editorial->isVisibleDescription;
        $description=$editorial->description;
        $photoUrl=$editorial->photoUrl;
        $isVisiblePhotoUrl=$editorial->isVisiblePhotoUrl;
        $bodyEvent=$editorial->bodyEvent;
        $isVisibleBodyEvent=$editorial->isVisibleBodyEvent;
        $note=$editorial->note;
        $isVisibleNote=$editorial->isVisibleNote;
        $socialId=$editorial->socialId;
        $editorialPlanId=$editorial->editorialPlanId;



        $res = "  Evento Azione Piano Editoriale Clonato";
        return $res;
    }
}