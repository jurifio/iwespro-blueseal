<?php

namespace bamboo\blueseal\jobs;

use bamboo\blueseal\remote\readextdbtable\CReadExtDbTable;
use bamboo\domain\entities\CNewsletter;
use bamboo\domain\entities\COrder;
use bamboo\core\jobs\ACronJob;
use bamboo\domain\repositories\CNewsletterRepo;

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
     * @throws \bamboo\core\exceptions\BambooException
     */
    public function run($args = null)
    {
        $sql = "SELECT * FROM Newsletter WHERE DATE_FORMAT(now(),'%Y%m%d%H%i') = DATE_FORMAT(sendAddressDate, '%Y%m%d%H%i')";
        /** @var CNewsletterRepo $newslettersRepo */
        $newslettersRepo = \Monkey::app()->repoFactory->create('Newsletter');
        $newsletters = $newslettersRepo->findBySql($sql);
        if (empty($newsletters)) return;
        $this->report('Starting', 'Newsletters to send: ' . count($newsletters));

        /** @var CNewsletter $newsletter */
        foreach ($newsletters as $newsletter) {

            if ($newsletter->newsletterInsertion->newsletterEvent->newsletterCampaign->newsletterShopId != 1) {
                $newsletterShopId = $newsletter->newsletterCampaign->newsletterShop->id;
                $fieldName = $newsletterShopId != 2 ? 'NewsletterUser' : 'Newsletter';
                $readExternalDb = new CReadExtDbTable($newsletterShopId);
                $readExternalDb->insertData(
                    false,
                    [$fieldName,
                        'UserDetails-Left' => [
                            'Self' => [
                                'userId'
                            ],
                            $fieldName => [
                                'userId'
                            ]
                        ]
                    ],
                    ['email', 'isActive', 'name', 'surname', 'birthDate'],
                    ['email'],
                    [],
                    'NewsletterExternalUser',
                    ['email', 'isActive', 'name', 'surname', 'birthDate'],
                    ['email', ['externalShopId' => $newsletterShopId]],
                    ['externalShopId' => $newsletterShopId]
                );
            }

            $asd = $newslettersRepo->sendNewsletterEmails($newsletter, ENV !== 'prod');

            $this->report('Esito Invio: ' . $newsletter->id, $asd);
        }
        $this->report('Ending', 'inviate tutte le newsletter');
    }

}