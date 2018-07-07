<?php
/**
 * Created by PhpStorm.
 * User: jurif
 * Date: 08/01/2018
 * Time: 16:55
 */

namespace bamboo\controllers\back\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\core\db\pandaorm\repositories\CRepo;
use bamboo\domain\entities\CWishList;
use bamboo\domain\entities\CUser;

class CWishListDetailAjaxController extends AAjaxController
{

    public function get()
    {
        $user =\Monkey::app()->
        $sql = "SELECT
  u.id,
  ud.name AS name,
  ud.surname AS surname,
  u.email AS email
FROM User u
  INNER  JOIN  UserDetails ud ON u.id = ud.userId
  INNER  JOIN  WishList W ON u.id = W.UserId
GROUP BY  u.id
                    ";

        $datatable = new CDataTables($sql, ['id'], $_GET, true);

        $datatable->doAllTheThings(true);

        foreach ($datatable->getResponseSetData() as $key=>$row) {




        }

        return $datatable->responseOut();

    }
}