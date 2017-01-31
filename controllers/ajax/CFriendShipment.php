<?php
namespace bamboo\blueseal\controllers\ajax;

use bamboo\core\exceptions\BambooException;
use bamboo\domain\repositories\CAddressBookRepo;
use bamboo\domain\repositories\COrderLineRepo;
use bamboo\utils\time\SDateToolbox;
use bamboo\utils\time\STimeToolbox;

/**
 * Class CFriendShipment
 * @package bamboo\blueseal\controllers\ajax
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
class CFriendShipment extends AAjaxController
{

    /**
     * A partire da alcune righe d'ordine verifica la prossima data di spedizione disponibile
     * fromAddressBookId: addressSelect.val(),
    carrierId: carrierSelect.val()
     */
    public function get()
    {
        $possibleDates = [];
        $fromAddressBookId = $this->app->router->request()->getRequestData('fromAddressBookId');
        $carrierId = $this->app->router->request()->getRequestData('carrierId');

        /** @var CAddressBookRepo $addressBookRepo */
        $addressBookRepo = $this->app->repoFactory->create('AddressBook');
        $toAddressBook = $addressBookRepo->getMainHubAddressBook();
        $lastPrenotationTime = '11:00';
        $existingShipment = $this->app->repoFactory->create('Shipment')->findBySql(
                                                                   "SELECT id
                                                                    FROM Shipment
                                                                    WHERE current_time < TIME(?) AND
                                                                          date(shipmentDate) = DATE(CURRENT_TIMESTAMP) AND
                                                                          carrierId = ? AND
                                                                          fromAddressBookId = ? AND
                                                                          toAddressBookId = ?",
            [$lastPrenotationTime,$carrierId,$fromAddressBookId,$toAddressBook->id]);


        if(!$existingShipment->isEmpty()) {
            $possibleDates[] = date('Y-m-d');
        }
        $next = SDateToolbox::GetNextWorkingDay(new \DateTime());
        $possibleDates[] = $next->format('Y-m-d');

        return json_encode($possibleDates);
    }

    public function post() {
        $dba = \Monkey::app()->dbAdapter;

        $request = \Monkey::app()->router->request();
        $orderLines = $request->getRequestData('rows');

        $res['error'] = false;
        $res['message'] = 'La riga o le righe d\'ordine selezionate sono state contrassegnate come spedite';
        /** @var COrderLineRepo $olR */
        $olR = \Monkey::app()->repoFactory->create('OrderLine');
        $lR = \Monkey::app()->repoFactory->create('Log');
        try {
            $dba->beginTransaction();

            if (is_string($orderLines)) $orderLines = [$orderLines];

            foreach($orderLines as $s) {
                $ol = $olR->findOneByStringId($s);
                if (!$ol) {
                    throw new BambooException('La riga d\'ordine fornita non esiste');
                } else if ('ORD_FRND_OK' !== $ol->status) {
                    throw new BambooException('La riga d\'ordine fornita deve avere lo stato "Accettato dal friend"');
                }

                $l =$lR->findOneBy(['actionName' => 'ShippedByFriend', 'entityName' => 'OrderLine', 'stringId' => $s]);

                if ($l) {
                    $res['error'] = true;
                    $res['message'] = 'La riga d\'ordine <strong>$s</strong> era già contrassegnata come spedita. Operazione annullata';
                    return json_encode($res);
                }

                $olR->updateStatus($ol, 'ORD_FRND_ORDSNT');

                $date = STimeToolbox::AngloFormattedDateTime();
                $l = $lR->getEmptyEntity();
                $l->userId = \Monkey::app()->getUser()->id;
                $l->entityName = 'OrderLine';
                $l->stringId = $s;
                $l->eventName = 'FriendOrderChangePaymentStatus';
                $l->actionName = 'ShippedByFriend';
                $l->eventValue = $date;
                $l->backTrace = 'FriendOrderChangePaymentStatus->post()';
                $l->time = $date;
                $l->insert();
            }
            $dba->commit();
            return json_encode($res);
        } catch (BambooException $e) {
            $dba->rollBack();
            \Monkey::app()->router->response()->raiseProcessingError();
            return $e->getMessage();
        }
    }
}