<?php
namespace bamboo\controllers\back\ajax;

use bamboo\core\exceptions\BambooException;

/**
 * Class CProductListAjaxController
 * @package bamboo\blueseal\controllers\ajax
 *
 * @author Iwes  International Web Ecommerce ServicesTeam <juri@iwes.it>, ${DATE}
 *
 * @copyright (c) Iwes International Web Ecommerce Services - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @since ${VERSION}
 */
class CFriendOrderChangePaymentStatus extends AAjaxController
{
    public function get()
    {
        $orderLines = \Monkey::app()->router->request()->getRequestData('orderLines');
        $olR = \Monkey::app()->repoFactory->create('OrderLine');
        $status = NULL;

        foreach($orderLines as $k => $v) {
            $orderLines[$k] = $olR->findOneByStringId($v);
        }
        foreach($orderLines as $v) {
            if (NULL === $status) $status = $v->orderLineFriendPaymentStatusId;
            elseif ($status !== $v->orderLineFriendPaymentStatusId) {
                $status = 0;
                break;
            }
        }

        $time = null;
        foreach($orderLines as $v) {
            if (NULL === $time) $time = $v->orderLineFriendPaymentDate;
            elseif ($time !== $v->orderLineFriendPaymentDate) {
                $time = NULL;
                break;
            }
        }
        if (NULL === $time) date('Y-m-d');

        $olfpsR = \Monkey::app()->repoFactory->create('OrderLineFriendPaymentStatus');
        $options = $olfpsR->findAllToArray();

        return json_encode([
            'time' => $time,
            'selected' => $status,
            'options' => $options
        ]);
    }

    public function post() {
        $dba = \Monkey::app()->dbAdapter;

        $request = \Monkey::app()->router->request();
        $orderLines = $request->getRequestData('orderLines');
        $newStatus = $request->getRequestData('friendPaymentStatus');
        $date = $request->getRequestData('friendPaymentDate');

        $unixDate = strtotime($date);
        try {
            \Monkey::app()->repoFactory->beginTransaction();

            if (!$unixDate) throw new BambooException('Non riconosco il formato della data');
            if (!\Monkey::app()->repoFactory->create('OrderLineFriendPaymentStatus')->findOneBy(['id' => $newStatus])) {
                throw new BambooException('Lo stato che si sta cercando di impostare non esiste');
            }

            $olR = \Monkey::app()->repoFactory->create('OrderLine');

            if (is_string($orderLines)) $orderLines = [$orderLines];

            foreach($orderLines as $s) {
                $olR->updateFriendPaymentStatus($s, $newStatus, $date);
            }

            \Monkey::app()->repoFactory->commit();
            return 'Lo stato di pagamento delle righe selezionate è stato modificato';
        } catch (BambooException $e) {
            \Monkey::app()->repoFactory->rollback();
            \Monkey::app()->router->response()->raiseProcessingError();
            return $e->getMessage();
        }
    }
}