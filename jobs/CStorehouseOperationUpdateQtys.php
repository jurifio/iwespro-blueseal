<?php
namespace bamboo\blueseal\jobs;

use bamboo\core\exceptions\BambooException;
use bamboo\core\jobs\ACronJob;

/**
 * Class CDispatchPreorderToFriend
 * @package bamboo\blueseal\jobs
 * @author Bambooshoot Team <emanuele@bambooshoot.agency>, ${DATE}
 * @copyright (c) Bambooshoot snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @since ${VERSION}
 */
class CStatisticsGenerateFilesForQlik extends ACronJob
{
    /**
     * @param null $args
     */
    public function run($args = null)
    {
        try {
            \Monkey::app()->repoFactory->create('StorehouseOperation')->updateStocksOnOperationTime();
        }catch(BambooException $e) {
            $this->error('Aggiornamento disponibilità dai movimenti', $e->getMessage());
        }
    }
}