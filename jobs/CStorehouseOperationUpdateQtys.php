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
class CStorehouseOperationUpdateQtys extends ACronJob
{
    /**
     * @param null $args
     */
    public function run($args = null)
    {
        $dba = \Monkey::app()->dbAdapter;
        \Monkey::app()->repoFactory->beginTransaction();
        try {
            \Monkey::app()->repoFactory->create('StorehouseOperation')->updateStocksOnOperationTime();
            \Monkey::app()->repoFactory->commit();
        }catch(BambooException $e) {
            \Monkey::app()->repoFactory->rollback();
            $this->error('Aggiornamento disponibilità dai movimenti', $e->getMessage());
        }
    }
}