<?php

namespace bamboo\domain\repositories;

use bamboo\core\base\CObjectCollection;
use bamboo\core\db\pandaorm\repositories\ARepo;
use bamboo\core\exceptions\BambooException;
use bamboo\domain\entities\CContractDetails;
use bamboo\domain\entities\CFoison;
use bamboo\domain\entities\CProductBatch;
use bamboo\domain\entities\CProductBatchDetails;
use bamboo\domain\entities\CProductBatchHasProductName;
use bamboo\domain\entities\CProductBatchTextManage;
use bamboo\domain\entities\CProductBatchTextManagePhoto;
use bamboo\domain\entities\CUser;
use bamboo\domain\entities\CWorkCategory;
use bamboo\domain\entities\CWorkCategorySteps;
use bamboo\utils\time\SDateToolbox;
use bamboo\utils\time\STimeToolbox;

/**
 * Class CProductBatchTextManagePhotoRepo
 * @package bamboo\domain\repositories
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 05/12/2018
 * @since 1.0
 */
class CProductBatchTextManagePhotoRepo extends ARepo
{

    public function insertNewProductBatchTextManagePhoto($productBatchTextManageId, string $imageName, bool $dummy) : CProductBatchTextManagePhoto {

        /** @var CProductBatchTextManagePhoto $newTextManagePhoto */
        $newTextManagePhoto = $this->getEmptyEntity();
        $newTextManagePhoto->imageName = $imageName;
        $newTextManagePhoto->productBatchTextManageId = $productBatchTextManageId;
        $newTextManagePhoto->isDummy = $dummy ? 1 : 0;
        $newTextManagePhoto->smartInsert();

        return $newTextManagePhoto;
    }

}