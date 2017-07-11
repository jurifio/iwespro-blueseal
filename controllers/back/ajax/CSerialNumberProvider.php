<?php

namespace bamboo\controllers\back\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\core\base\CSerialNumber;
use bamboo\domain\entities\CAddressBook;

/**
 * Class CSerialNumberProvider
 * @package bamboo\controllers\back\ajax
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
class CSerialNumberProvider extends AAjaxController
{
    public function get()
    {
        $serial = new CSerialNumber();
        $serial->generate();
        return $serial;
    }
}