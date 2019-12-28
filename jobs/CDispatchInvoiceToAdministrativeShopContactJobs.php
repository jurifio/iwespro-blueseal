<?php
namespace bamboo\blueseal\jobs;

use bamboo\core\base\CObjectCollection;
use bamboo\core\jobs\ACronJob;
use bamboo\domain\repositories\COrderLineRepo;
use bamboo\export\order\COrderExport;
use bamboo\core\db\pandaorm\repositories\CRepo;
use bamboo\domain\repositories\CEmailRepo;

/**
 * Class CDispatchInvoiceToAdministrativeShopContactJobs
 * @package bamboo\blueseal\jobs
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 28/12/2019
 * @since 1.0
 */
class CDispatchInvoiceToAdministrativeShopContactJobs extends ACronJob
{

    var $success = "ORD_FRND_SENT";
    var $fail = "ORD_ERR_SEND";

    /**
     * @param null $args
     */
    public function run($args = null)
    {
        $shops = \Monkey::app()->repoFactory->create('Shop')->findBy(['hasEcommerce' => 1]);
      $invoiceRepo=\Monkey::app()->repoFactory->create('Invoice');
        $today = new \DateTime();
        $dateSend = $today->format('d-m-Y');

        foreach($shops as $shop){
            $bodyMail='Invio Elenco Documenti  Emessi per il giorno '.$dateSend;
            $to=[$shop->billingContact];
            $invoice=$invoiceRepo
        }
    }
}