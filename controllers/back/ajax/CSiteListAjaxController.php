<?php
/**
 *
 */

namespace bamboo\controllers\back\ajax;

use bamboo\core\db\pandaorm\repositories\ARepo;

use bamboo\blueseal\business\CDataTables;
use bamboo\core\base\CObjectCollection;
use bamboo\core\db\pandaorm\repositories\CRepo;
use bamboo\domain\entities\CWishList;
use bamboo\domain\entities\CUser;
use bamboo\domain\entities\CProduct;
use bamboo\domain\entities\CSiteApi;
use bamboo\domain\entities\CSite;
use bamboo\domain\entities\CProductPublicSku;
use bamboo\domain\entities\CCartLine;
use bamboo\core\base\CSerialNumber;
use bamboo\domain\entities\CCartAbandonedEmailSend;
use bamboo\domain\repositories\CCartAbandonedEmailSendRepo;
use bamboo\utils\time\STimeToolbox;
use bamboo\utils\price\SPriceToolbox;
use bamboo\core\events\AEventListener;

class CSiteListAjaxController extends AAjaxController
{

    /**
     *
     */
    public function get()
    {
        $sql = "SELECT id as id, shopId as shopId   FROM SiteApi ";

        $datatable = new CDataTables($sql, ['id'], $_GET, true);

        $datatable->doAllTheThings('true');

        foreach ($datatable->getResponseSetData() as $key => $row) {

            $datatable->setResponseDataSetRow($key, $row);

        }

        return $datatable->responseOut();
    }


}