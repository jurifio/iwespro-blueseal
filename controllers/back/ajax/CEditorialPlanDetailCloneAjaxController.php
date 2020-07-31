<?php


namespace bamboo\controllers\back\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\core\db\pandaorm\repositories\CRepo;
use bamboo\domain\entities\CEditorialPlan;
use bamboo\domain\entities\CEditorialPlanArgument;
use bamboo\domain\entities\CEditorialPlanDetail;
use \bamboo\utils\time\STimeToolbox;

/**
 * Class CEditorialPlanDetailCloneAjaxController
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

class CEditorialPlanDetailCloneAjaxController extends AAjaxController
{



    public function put()
    {
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
        $editorialInsert = \Monkey::app()->repoFactory->create('editorialPlanDetail')->getEmptyEntity();
        $editorialInsert->startEventDate = $startEventDate;
        $editorialInsert->endEventDate = $endEventDate;
        $editorialInsert->isEventVisible = $isEventVisible;
        $editorialInsert->editorialPlanArgumentId = $editorialPlanArgumentId;
        $editorialInsert->isVisibleEditorialPlanArgument = $isVisibleEditorialPlanArgument;
        $editorialInsert->title = $title;
        $editorialInsert->isVisibleDescription = $isVisibleDescription;
        $editorialInsert->description = $description;
        $editorialInsert->photoUrl = $photoUrl;
        $editorialInsert->isVisiblePhotoUrl = $isVisiblePhotoUrl;
        $editorialInsert->status='Draft';
        $editorialInsert->bodyEvent = $bodyEvent;
        $editorialInsert->isVisibleBodyEvent = $isVisibleBodyEvent;
        $editorialInsert->note = $note;
        $editorialInsert->isVisibleNote = $isVisibleNote;
        $editorialInsert->socialId = $socialId;
        $editorialInsert->editorialPlanId = $editorialPlanId;
        $editorialInsert->insert();


        $res = "  Evento Azione Piano Editoriale Clonato";
        return $res;
    }
}