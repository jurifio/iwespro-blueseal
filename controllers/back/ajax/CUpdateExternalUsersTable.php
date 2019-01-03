<?php

namespace bamboo\controllers\back\ajax;
use bamboo\blueseal\remote\readextdbtable\CReadExtDbTable;


/**
 * Class CUpdateUsersTableFromExternal
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 26/07/2018
 * @since 1.0
 */
class CUpdateExternalUsersTable extends AAjaxController
{

    /**
     * @return bool|string
     * @throws \bamboo\core\exceptions\BambooDBALException
     * @throws \bamboo\core\exceptions\BambooException
     */
    public function post(){

        $newsletterShopId = \Monkey::app()->router->request()->getRequestData('newsletterShopId');

        //$newsletterShop = \Monkey::app()->repoFactory->create('NewsletterShop')->findOneBy(['id'=>$newsletterShopId]);

        $readExternalDb = new CReadExtDbTable($newsletterShopId);
        $ins = $readExternalDb->insertData(
            false,
            ['NewsletterUser',
                'UserDetails-Left'=>[
                    'Self'=>[
                        'userId'
                    ],
                    'NewsletterUser'=>[
                        'userId'
                    ]
                ]
            ],
            ['email', 'isActive','name','surname','birthDate'],
            ['email'],
            [],
            'NewsletterExternalUser',
            ['email', 'isActive','name','surname','birthDate'],
            ['email', ['externalShopId' => $newsletterShopId]],
            ['externalShopId' => $newsletterShopId]
        );

        if(is_bool($ins)) return 'Lista aggiornata correttamente';

        return $ins;
    }
}