<?php

namespace bamboo\controllers\back\ajax;

use bamboo\core\base\CObjectCollection;
use bamboo\core\exceptions\BambooException;
use bamboo\core\exceptions\BambooOrderLineException;
use bamboo\core\exceptions\BambooShipmentException;
use bamboo\domain\entities\COrderLine;
use bamboo\domain\repositories\COrderLineRepo;
use bamboo\domain\repositories\CShipmentRepo;
use bamboo\utils\time\STimeToolbox;

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
    public function get()
    {
        $addresses = [];
        foreach ($this->app->getUser()->getAuthorizedShops() as $shop) {
            foreach ($shop->shippingAddressBook as $addressBook) {
                $addressBook->shopId = $shop->id;
                $addressBook->shopName = $shop->name;
                $addressBook->shopTitle = $shop->title;
                $addresses[] = $addressBook;
            }
        }
        return json_encode($addresses);
    }

    /**
     * @return BambooException|BambooOrderLineException|\Exception|string
     * @transaction
     */
    public function post()
    {
        $dba = \Monkey::app()->dbAdapter;

        $request = \Monkey::app()->router->request();
        $orderLines = $request->getRequestData('rows');
        $response = $request->getRequestData('response');

        \Monkey::app()->repoFactory->beginTransaction();
        try {

            if (FALSE == $response) {
                throw new BambooException('"response" non pervenuto');
            }

            if ('ok' === $response) {
                $newStatus = 'ORD_FRND_OK';
                $verdict = 'Consenso';
            } elseif ('ko' === $response) {
                $newStatus = 'ORD_FRND_CANC';
                $verdict = 'Rifiuto';
            }

            /** @var COrderLineRepo $olR */
            $olR = \Monkey::app()->repoFactory->create('OrderLine');

            if (is_string($orderLines)) $orderLines = [$orderLines];

            $orderLineCollection = new CObjectCollection();
            foreach ($orderLines as $o) {
                /** @var COrderLine $ol */
                $ol = $olR->findOneByStringId($o);
                $orderLineCollection->add($ol);
                if (!$ol) {
                    throw new BambooException('La linea ordine ' . $o . ' non esiste');
                }
                $olR->setFriendVerdict($ol, $newStatus);
                if ($ol->shipment->count() && 'Rifiuto' == $newStatus) {
                    $shipment = $ol->shipment->getLast();
                    if ($shipment->shipmentDate)
                        throw new BambooOrderLineException(
                            'La riga d\'ordine <strong>' . $ol->stringId() . '</strong> è già stata spedita e non può essere annullata'
                        );
                    if (!$shipment->cancellationDate) {
                        $shipment->cancellationDate = STimeToolbox::DbFormattedDate();
                        $shipment->shipmentFaultId = 3;
                        $shipment->update();
                    }
                }
            }

            if ($verdict == 'Consenso') {
                $fromAddressBookId = $request->getRequestData('fromAddressBookId');
                $carrierId = $request->getRequestData('carrierId');
                $shippingDate = $request->getRequestData('shippingDate');
                $bookingNumber = $request->getRequestData('bookingNumber');
                $bookingNumber = empty($bookingNumber) ? null : $bookingNumber;
                /** @var CShipmentRepo $shipmentRepo */
                $shipmentRepo = \Monkey::app()->repoFactory->create('Shipment');
                $shipment = $shipmentRepo->newOrderShipmentFromSupplierToClient($carrierId, $fromAddressBookId, $bookingNumber, $shippingDate, $orderLineCollection);
                $request->getRequestData();
                $this->app->eventManager->triggerEvent('orderLine.friend.accept', ['orderLines' => $orderLineCollection]);

                \Monkey::app()->repoFactory->commit();
                return json_encode(['error' => false, 'message' => $verdict . ' correttamente registrato', 'shipmentId' => $shipment->id]);
            } else {

                \Monkey::app()->repoFactory->commit();
                return json_encode(['error' => false, 'message' => $verdict . ' correttamente registrato']);
            }

        } catch (BambooOrderLineException $e) {
            \Monkey::app()->repoFactory->rollback();
            $message = 'OOPS! Le operazioni richieste non sono state eseguite:<br />';
            return json_encode(['error' => true, 'message' => $message . $e->getMessage()]);
        } catch (BambooException $e) {
            \Monkey::app()->repoFactory->rollback();
            \Monkey::app()->router->response()->raiseProcessingError();
            $message = 'OOPS! Le operazioni richieste non sono state eseguite:<br />';
            return json_encode(['error' => true, 'message' => $message . $e->getMessage()]);
        }
    }
}