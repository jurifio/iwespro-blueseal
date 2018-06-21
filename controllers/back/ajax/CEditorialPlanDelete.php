<?php

namespace bamboo\controllers\back\ajax;

use bamboo\core\db\pandaorm\repositories\CRepo;
use bamboo\core\exceptions\BambooException;
use bamboo\domain\entities\CEditorialPlan;
use bamboo\domain\repositories\CEditorialPlanRepo;


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
class CEditorialPlanDelete extends AAjaxController
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
        if (strlen($id)>10) {
            $finalpositionId = strpos($id, '</a>');
            $initialpositionId = strpos($id, '">');
            $finalpositionId = $finalpositionId;
            $initialpositionId = $initialpositionId + 2;
            $lenghtposition = $finalpositionId - $initialpositionId;
            $id = substr($id, $initialpositionId, $lenghtposition);
        }
        /** @var CRepo $editorialPlan */
        $editorialPlan = \Monkey::app()->repoFactory->create('editorialPlan');

        /** @var CEditorialPlan $editorial */
        $editorial= $editorialPlan->findOneBy(['id'=>$id]);
        $editorial->delete();
        $res = "Piano Editoriale Cancellato";
        return $res;

    }



}