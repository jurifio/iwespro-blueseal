<?php
namespace bamboo\blueseal\jobs;

use bamboo\domain\entities\COrder;
use bamboo\core\jobs\ACronJob;
use bamboo\domain\repositories\CCartAbandonedSendEmailRepo;

/**
 * Class CNewsletterSend
 * @package bamboo\blueseal\jobs
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 03/02/2018
 * @since 1.0
 */
class CNewsletterSend extends ACronJob
{
    /**
     * @param null $args
     * @throws \bamboo\core\exceptions\BambooDBALException
     */
    public function run($args = null)
    {
        $sql = "Select * from Newsletter where DATE_FORMAT(now(),'%Y%m%d%H%i') = DATE_FORMAT(sendAddressDate, '%Y%m%d%H%i')";
        /** @var CNewsletterRepo $newslettersRepo */
        $newslettersRepo = \Monkey::app()->repoFactory->create('Newsletter');
        $newsletters = $newslettersRepo->findBySql($sql);
        if(empty($newsletters)) return;
        $this->report('Starting','Newsletters to send: '.count($newsletters));
        foreach ($newsletters as $newsletter) {
            $asd = $newslettersRepo->sendNewsletterEmails($newsletter, ENV !== 'prod',true);
            $this->report('Esito Invio: '.$newsletter->id, $asd);
        }
        $this->report('Ending', 'inviate tutte le newsletter');
    }

}