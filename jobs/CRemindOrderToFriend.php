<?php
namespace bamboo\blueseal\jobs;

use bamboo\core\base\CObjectCollection;
use bamboo\core\email\CEmail;
use bamboo\core\jobs\ACronJob;
use bamboo\domain\repositories\CEmailRepo;
use bamboo\domain\repositories\COrderLineRepo;
use bamboo\export\order\COrderExport;
use bamboo\core\db\pandaorm\repositories\CRepo;

/**
 * Class CRemindOrderToFriend
 * @package bamboo\blueseal\jobs
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
class CRemindOrderToFriend extends ACronJob
{
    /**
     * @param null $args
     */
    public function run($args = null)
    {
        $shops = $this->app->repoFactory->create('Shop')->findAll();
        $query = "SELECT * from OrderLine where `status` in ('ORD_FRND_SENT') AND shopId = ? ";

        /** @var COrderLineRepo $orderLineRepo */
        $orderLineRepo = $this->app->repoFactory->create('OrderLine');

        foreach($shops as $shop){
            try {
                $lines = $orderLineRepo->em()->findBySql($query, [$shop->id]);
                $this->report('Working Shop ' . $shop->name . ' Start', 'Found ' . count($lines) . ' to send');

                if (isset($shop->referrerEmails) && count($lines) >0 ) {
                    $to = explode(';',$shop->referrerEmails);

                    /*$this->app->mailer->prepare('friendorderreminder','no-reply', $to,[],[],
                        ['orderLines'=>$lines]);
                    $this->app->mailer->send();*/

                    /** @var CEmailRepo $emailRepo */
                    $emailRepo = \Monkey::app()->repoFactory->create('Email');
                    $emailRepo->newPackagedMail('friendorderreminder','no-reply@pickyshop.com', $to,[],[],
                        ['orderLines'=>$lines]);

                    $this->report('Working Shop ' . $shop->name . ' End', 'Reminder Sent ended');
                }
                
            } catch(\Throwable $e){
                $this->error( 'Working Shop ' . $shop->name . ' End', 'ERROR Sending Lines',$e);
            }
        }
    }
}