<?php

namespace bamboo\controllers\back\ajax;

use bamboo\core\base\CObjectCollection;
use bamboo\core\exceptions\BambooException;
use bamboo\core\exceptions\BambooOrderLineException;
use bamboo\core\exceptions\BambooShipmentException;
use bamboo\domain\entities\COrderLine;
use bamboo\domain\repositories\COrderLineRepo;
use bamboo\domain\repositories\CShipmentRepo;
use bamboo\utils\time\STimeToolbox;
use DateTime;
use PDO;
use PDOException;

/**
 * Class CSelectBillRegistryGroupProductAjaxController
 * @package bamboo\controllers\back\ajax
 */
class CSelectBillRegistryGroupProductAjaxController extends AAjaxController
{
    public function get()
    {
        $selectProduct=[];
        $accountAsService = $this -> app -> router -> request() -> getRequestData('accountAsService');
        $res = $this -> app -> dbAdapter -> query('SELECT brp.id as id, brp.codeProduct as codeProduct, brcp.name as categoryName, brp.name as nameProduct
        from BillRegistryGroupProduct brp join BillRegistryCategoryProduct brcp on brp.billRegistryCategoryProductId=brcp.id
        ', []) -> fetchAll();

        foreach ($res as $result) {
            $selectProduct[] = ['id' => $result['id'], 'codeProduct' => $result['codeProduct'], 'nameProduct' => $result['nameProduct'], 'categoryName' => $result['categoryName']];
        }

        return json_encode($selectProduct);
    }
}