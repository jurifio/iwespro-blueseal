<?php
namespace bamboo\blueseal\controllers\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\core\exceptions\BambooException;
use bamboo\core\exceptions\BambooOrderLineException;
use bamboo\core\intl\CLang;
use bamboo\domain\repositories\CLogRepo;
use bamboo\domain\repositories\COrderLineRepo;

/**
 * Class CProductListAjaxController
 * @package bamboo\blueseal\controllers\ajax
 *
 * @author Bambooshoot Team <emanuele@bambooshoot.agency>, ${DATE}
 *
 * @copyright (c) Bambooshoot snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @since ${VERSION}
 */
class CFriendAccept extends AAjaxController
{
    /**
     * @return BambooException|BambooOrderLineException|\Exception|string
     * @transaction
     */
    public function post() {
        $dba = \Monkey::app()->dbAdapter;

        $request = \Monkey::app()->router->request();
        $orderLines = $request->getRequestData('rows');
        $response = $request->getRequestData('response');

        $dba->beginTransaction();
        try {

            if (FALSE == $response) {
                throw new BambooException('"response" non pervenuto');
            }

            if ('ok' === $response) {
                $newStatus = 'ORD_FRND_OK';
                $verdict = 'Consenso';
                $sendReminderMail = false;
            } elseif ('ko' === $response) {
                $newStatus = 'ORD_FRND_CANC';
                $verdict = 'Rifiuto';
                $sendReminderMail = true;
            }

            /** @var COrderLineRepo $olR */
            $olR = \Monkey::app()->repoFactory->create('OrderLine');

            if (is_string($orderLines)) $orderLines = [$orderLines];

            foreach ($orderLines as $o) {
                $id = explode('-', $o);
                $ol = $olR->findOneBy(['id' => $id[0], 'orderId' => $id[1]]);
                if (!$ol) {
                    throw new BambooException('La linea ordine ' . $o . ' non esiste');
                }
                $olR->setFriendVerdict($ol, $newStatus, $sendReminderMail);
            }
            $dba->commit();
            return $verdict . ' correttamente registrato';
        } catch (BambooOrderLineException $e) {
            $dba->rollback();
            $message = 'OOPS! Le operazioni richieste non sono state eseguite:<br />';
            return $message . $e->getMessage();
        } catch (BambooException $e) {
            $dba->rollBack();
                \Monkey::app()->router->response()->raiseProcessingError();
                return $e;
        }
    }
}