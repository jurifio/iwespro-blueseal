<?php
/**
 *
 */

namespace bamboo\controllers\back\ajax;

use bamboo\core\db\pandaorm\repositories\ARepo;
use bamboo\core\jobs\ACronJob;

use bamboo\blueseal\business\CDataTables;
use bamboo\core\base\CObjectCollection;
use bamboo\core\db\pandaorm\repositories\CRepo;
use bamboo\domain\entities\CWishList;
use bamboo\domain\entities\CUser;
use bamboo\domain\entities\CProduct;
use bamboo\domain\entities\CCart;
use bamboo\domain\entities\CProductPublicSku;
use bamboo\domain\entities\CCartLine;
use bamboo\core\base\CSerialNumber;
use bamboo\domain\entities\CCartAbandonedEmailSend;
use bamboo\domain\repositories\CCartAbandonedEmailSendRepo;
use bamboo\utils\time\STimeToolbox;
use bamboo\utils\price\SPriceToolbox;
use bamboo\core\events\AEventListener;

class CCartAbandonedEmailParamListAjaxController extends AAjaxController
{

    /**
     *
     */
    public function get()
    {
        $sql = "SELECT
  C.id                                                   AS id,
  C.name                                         AS name,
  nt1.name                                        AS firstTemplate,
  nt2.name                                        AS secondTemplate,
  nt3.name                                        AS thirdTemplate
FROM CartAbandonedEmailParam C
  INNER JOIN NewsletterTemplate nt1 ON C.firstTemplateId = nt1.id
  INNER JOIN NewsletterTemplate nt2 ON C.secondTemplateId = nt2.id
  INNER JOIN NewsletterTemplate nt3 ON C.thirdTemplateId = nt3.id
  ";

        $datatable = new CDataTables($sql, ['id'], $_GET, true);

        $datatable->doAllTheThings(true);

        foreach ($datatable->getResponseSetData() as $key => $row) {
            $val = \Monkey ::app() -> repoFactory -> create('CartAbandonedEmailParam') -> findOneBy($row);
            $row['DT_RowId'] = $val -> printId();
            $row['id'] = '<a href="/blueseal/cartabandoned/edit-plan/' . $val -> printId() . '">' . $val -> printId() . '</a>';





            $datatable->setResponseDataSetRow($key, $row);


        }

        return $datatable->responseOut();
    }







}