<?php

namespace bamboo\blueseal\jobs;

use bamboo\domain\entities\CCartAbandonedEmailSend;
use bamboo\domain\repositories\CCartAbandonedEmailSendRepo;
use bamboo\domain\entities\COrder;
use bamboo\domain\entities\CCurrency;
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
 * Class CUpdateCurrencyRatesJob
 * @package bamboo\blueseal\jobs
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 13/08/2021
 * @since 1.0
 */
class CUpdateCurrencyRatesJob extends ACronJob
{
    /**
     * @param null $args
     * @throws \bamboo\core\exceptions\BambooDBALException
     */
    public function run($args = null)
    {
        $this->report('CUpdateCurrencyRatesJob','start update currency','');
        $access_key = \Monkey::app()->cfg()->fetch('exchangeratesapi', 'access_key');
        $url = 'http://api.exchangeratesapi.io/v1/latest?access_key='.$access_key.'&symbol=EUR,USD,AUD,PLN,MXN';
        $currencyRepo=\Monkey::app()->repoFactory->create('Currency');
        $rawdata = file_get_contents($url);
        $decodedArray = json_decode($rawdata, true);
        $rates=$decodedArray['rates'];
        foreach ($rates as $key => $value){
            $currency=$currencyRepo->findOneBy(['code'=>$key]);
            if($currency){
                $currency->conversionToEur=$value;
                $currency->update();
                $this->report('CUpdateCurrencyRatesJob','set currency: '.$key,'value Rate:'.$key);
            }
        }
        $this->report('CUpdateCurrencyRatesJob','End update currency','');

    }


}