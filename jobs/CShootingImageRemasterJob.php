<?php

namespace bamboo\blueseal\jobs;

use bamboo\domain\entities\CCartAbandonedEmailSend;
use bamboo\domain\repositories\CCartAbandonedEmailSendRepo;
use bamboo\domain\entities\COrder;
use bamboo\domain\entities\CCart;
use bamboo\domain\entities\CCartAbandonedEmailParam;
use bamboo\domain\entities\CCouponType;
use bamboo\domain\entities\CCoupon;
use bamboo\domain\entities\CCartLine;
use bamboo\core\base\CSerialNumber;
use bamboo\core\db\pandaorm\repositories\ARepo;
use bamboo\domain\repositories\CEmailRepo;
use bamboo\blueseal\remote\readextdbtable\CReadExtDbTable;
use PDO;
use prepare;

use bamboo\core\jobs\ACronJob;
use bamboo\domain\entities\CProductPublicSku;
use bamboo\domain\entities\CProduct;
use bamboo\core\events\AEventListener;


/**
 * Class CShootingImageRemasterJob
 * @package bamboo\blueseal\jobs
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 29/01/2019
 * @since 1.0
 */
class CShootingImageRemasterJob extends ACronJob
{
    /****
     * @param null $args
     * @return mixed|string
     * @throws \bamboo\core\exceptions\BambooDBALException
     ******/
    public function run($args = null)
    {

        set_time_limit(0);
        ini_set('memory_limit', '2048M');

        if (ENV == 'dev') {

            $save_to = '/media/sf_sites/PickyshopNew/temp-remaster/';

        } else {
            $save_to = '/home/iwespro/public_html/temp-remaster/';
        }

        /*****  connessione  ftp ******/
        $ftp_server = "fiber.office.iwes.it";
        $ftp_user_name = "shooting";
        $ftp_user_pass = "XtUWicJUrEXv";
        $remote_file = "/shootImport/incoming";

        $ftp_url = "ftp://" . $ftp_user_name . ":" . $ftp_user_pass . "@" . $ftp_server . $remote_file ;






        $res = "esportazione Nuovi Prodotti eseguito  finito alle ore " . date('Y-m-d H:i:s');
        $this->report('Exporting to Prestashop ', $res, $res);


        return $res;
    }


}