<?php
namespace bamboo\blueseal\controllers\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\core\exceptions\BambooException;
use bamboo\core\intl\CLang;
use bamboo\domain\repositories\CLogRepo;

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

    public function post() {
        $dba = \Monkey::app()->dbAdapter;

        $request = \Monkey::app()->router->request();
        $orderLines = $request->getRequestData('rows');
        $response = $request->getRequestData('response');


        $soR = \Monkey::app()->repoFactory->create('StorehouseOperation');
        $psR = \Monkey::app()->repoFactory->create('ProductSku');
        /** @var CLogRepo $lR */
        $lR = \Monkey::app()->repoFactory->create('Log');

        $is500 = true;
        $sendReminderMail = false;
        try {

            if (FALSE == $response) {
                $is500 = true;
                throw new BambooException('"response" non pervenuto');
            }

            if ('ok' === $response) {
                $newStatus = 'ORD_FRND_OK';
                $verdict = 'Consenso';
            }
            elseif ('ko' === $response) {
                $newStatus = 'ORD_FRND_CANC';
                $verdict = 'Rifiuto';
                $sendReminderMail = true;
            }

            $dba->beginTransaction();

            $olR = \Monkey::app()->repoFactory->create('OrderLine');

            if (is_string($orderLines)) $orderLines = [$orderLines];

            foreach($orderLines as $o) {
                $id = explode('-', $o);
                $ol = $olR->findOneBy(['id' => $id[0], 'orderId' => $id[1]]);
                if (!$ol) {
                    $is500 = false;
                    throw new BambooException('La linea ordine ' . $o . ' non esiste');
                }
                $statusId = $ol->orderLineStatus->id;
                if (4 > $statusId || 8 < $statusId) {
                    $is500 = false;
                    throw new BambooException('Lo stato della linea ordine ' . $o . ' non può essere aggiornato');
                }
                if ('ko' === $response && 'ORD_FRND_OK') {
                    $allShops = \Monkey::app()->getUser()->hasPermission('allShops');
                    if (!$allShops) {
                        $last = $lR->getLastEntry(
                            [
                                'stringId' => $o,
                                'eventValue' => 'ORD_FRND_OK'
                            ]
                        );
                        if ($last) {
                            $is500 = false;
                            throw new BambooException('La riga d\'ordine <strong>' . $o . '</strong> è stata precedentemente accettata e non può essere cancellata');
                        }
                    }
                }
                $ol->status = $newStatus;
                $ol->update();
                if($sendReminderMail) {
                    mail('friends@iwes.it','Rifiuto Friend',"L'utente {$this->app->getUser()->getFullName()} ha rifiutato l'ordine: {$ol->printId()} per il friend {$ol->shop->title}");
                }

                $accepted = ('ok' === $response) ? true : false;
                $psk = $psR->findOne([$ol->productId, $ol->productVariantId, $ol->productSizeId, $ol->shopId]);
                $soR->registerEcommerceSale($ol->shopId, [$psk], null, $accepted);
            }
            $dba->commit();

            return $verdict . ' correttamente registrato';
        } catch (BambooException $e) {
            $dba->rollBack();
            if ($is500) {
                \Monkey::app()->router->response()->raiseProcessingError();
            }
            $message = '';
            if (!$is500) $message = 'OOPS! Le operazioni richieste non sono state eseguite:<br />';
            return $message . $e->getMessage();
        }
    }
}