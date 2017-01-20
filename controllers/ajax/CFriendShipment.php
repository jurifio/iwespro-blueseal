<?php
namespace bamboo\blueseal\controllers\ajax;

use bamboo\core\exceptions\BambooException;
use bamboo\utils\time\STimeToolbox;

/**
 * Class CFriendShipment
 * @package bamboo\blueseal\controllers\ajax
 */
class CFriendShipment extends AAjaxController
{

    public function post() {
        $dba = \Monkey::app()->dbAdapter;

        $request = \Monkey::app()->router->request();
        $orderLines = $request->getRequestData('rows');

        $res['error'] = false;
        $res['message'] = 'La riga o le righe d\'ordine selezionate sono state contrassegnate come spedite';

        $olR = \Monkey::app()->repoFactory->create('OrderLine');
        $lR = \Monkey::app()->repoFactory->create('Log');
        try {
            $dba->beginTransaction();

            if (is_string($orderLines)) $orderLines = [$orderLines];

            foreach($orderLines as $s) {
                $ol = $olR->findOneByStringId($s);
                if (!$ol) {
                    throw new BambooException('La riga d\'ordine fornita non esiste');
                }

                $l =$lR->findOneBy(['actionName' => 'ShippedByFriend', 'entityName' => 'OrderLine', 'stringId' => $s]);

                if ($l) {
                    $res['error'] = true;
                    $res['message'] = 'La riga d\'ordine <strong>$s</strong> era già contrassegnata come spedita. Operazione annullata';
                    return json_encode($res);
                }

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