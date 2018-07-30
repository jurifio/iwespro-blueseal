<?php
namespace bamboo\controllers\back\ajax;
use bamboo\domain\entities\COrderLine;
use bamboo\domain\repositories\COrderLineRepo;

/**
 * Class CChangePriceFriendAjaxController
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 20/07/2018
 * @since 1.0
 */
class CChangePriceFriendAjaxController extends AAjaxController
{

    public function put(){
        $newPrice = \Monkey::app()->router->request()->getRequestData('newPrice');
        if(empty($newPrice) || $newPrice == 0) return 'Devi inserire un nuovo prezzo valido';

        $orderCode = \Monkey::app()->router->request()->getRequestData('order');

        /** @var COrderLineRepo $olR */
        $olR = \Monkey::app()->repoFactory->create('OrderLine');


        /** @var COrderLine $oL */
        $oL = $olR->findOneByStringId($orderCode);
        if($olR->changeFriendRevenue($oL,$newPrice)) return 'Prezzo friend aggiornato con successo';

        return 'Errore';
    }

}