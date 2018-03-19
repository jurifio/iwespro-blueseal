<?php
namespace bamboo\domain\repositories;

use bamboo\core\base\CObjectCollection;
use bamboo\core\db\pandaorm\repositories\ARepo;
use bamboo\core\exceptions\BambooException;
use bamboo\domain\entities\CProductBatch;
use bamboo\domain\entities\CWorkCategorySteps;

/**
 * Class CProductBatchRepo
 * @package bamboo\domain\repositories
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 16/03/2018
 * @since 1.0
 */
class CWorkCategoryStepsRepo extends ARepo
{
    /**
     * @param $id
     * @return CWorkCategorySteps|null
     */
    public function getFirstStepsFromCategoryId($id)
    {

        $firstStep = null;
        /** @var CObjectCollection $steps */
        $steps = $this->findBy(['workCategoryId'=>$id]);


        /** @var CWorkCategorySteps $step */
        foreach ($steps as $step){
            $firstStep = $step;
            break;
        }

        return $firstStep;
    }
}