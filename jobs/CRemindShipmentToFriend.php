<?php
namespace bamboo\blueseal\jobs;

use bamboo\core\base\CObjectCollection;
use bamboo\core\jobs\ACronJob;
use bamboo\domain\entities\CShipment;
use bamboo\domain\repositories\CEmailRepo;
use bamboo\domain\repositories\COrderLineRepo;
use bamboo\export\order\COrderExport;
use bamboo\core\db\pandaorm\repositories\CRepo;

/**
 * Class CRemindShipmentToFriend
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
class CRemindShipmentToFriend extends ACronJob
{
    /**
     * @param null $args
     */
    public function run($args = null)
    {
        $this->report('ShipmentRemind', 'starting remind to friends');
        $query = "SELECT distinct s.id
                  from 
                    Shipment s 
                    JOIN AddressBook ab on s.fromAddressBookId = ab.id
                    JOIN ShopHasShippingAddressBook shsab on shsab.addressBookId = ab.id
                    JOIN Shop on Shop.id = shsab.shopId
                  where s.predictedShipmentDate <= CURRENT_DATE
                  and scope = ?
                  and s.shipmentDate is null and s.cancellationDate is null and s.deliveryDate is null";
        $shops = \Monkey::app()->repoFactory->create('Shop')->findBySql($query,[CShipment::SCOPE_SUPPLIER_TO_US]);

        foreach($shops as $shop){
            try {
                $to = explode(';',$shop->referrerEmails);


               /* $this->app->mailer->prepare('friendshipmentreminder','no-reply', $to,[],[],['shop'=>$shop]);
                $this->app->mailer->send();*/

                /** @var CEmailRepo $emailRepo */
                $emailRepo = \Monkey::app()->repoFactory->create('Email');
                $emailRepo->newPackagedTemplateMail('friendshipmentreminder','no-reply@iwes.pro', $to,[],[],['shop'=>$shop]);


                $this->report('Working Shop ' . $shop->name . ' End', 'Reminder Sent ended');
            } catch(\Throwable $e){
                $this->error( 'Working Shop ' . $shop->name . ' End', 'ERROR Sending Lines',$e);
            }
        }
        $this->report('ShipmentRemind', 'done remind to friends');
        $this->report('ShipmentRemind', 'starting remind to us');
        $query = "SELECT distinct Shop.id
                  from 
                    Shipment s 
                    join AddressBook ab on s.fromAddressBookId = ab.id
                    JOIN ShopHasShippingAddressBook shsab on shsab.addressBookId = ab.id
                    JOIN Shop on Shop.id = shsab.shopId
                  where s.predictedShipmentDate = CURRENT_DATE - 1
                  and s.cancellationDate is null and s.deliveryDate is null";
        $shops = \Monkey::app()->repoFactory->create('Shop')->findBySql($query,[]);

        $names = [];
        foreach($shops as $shop){
            try {
                $names[] = $shop->title;
            } catch(\Throwable $e){
            }
        }
        if(count($names) > 0) {
            iwesMail('friends@iwes.it',
                'Mancate Spedizioni',
                'Attenzione, si segnala che alcune spedizioni previste per ieri non sono arrivate: '.implode(', ',$names));
        }
        $this->report('ShipmentRemind', 'all done');
    }
}