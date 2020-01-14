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
 * Class CSelectBrandMarketplaceAccountAjaxController
 * @package bamboo\controllers\back\ajax
 */
class CSelectBrandMarketplaceAccountAjaxController extends AAjaxController
{
    public function get()
    {
        $selectBrands=[];
        $shopId = $this -> app -> router -> request() -> getRequestData('shop');
        $res = $this -> app -> dbAdapter -> query('(SELECT pb.id as id,
        pb.name as brandName, s.name as shopName, s.id as shopIdOrigin, s.id AS shopIdDestination from ProductBrand pb
                                                      join Product p on pb.id=p.productBrandId
                                                      join  ProductSku ps on p.id =ps.productId and p.productVariantId=ps.productVariantId
                                                      join Shop s on ps.shopId=s.id
 WHERE ps.shopId = '.$shopId.' group by pb.name,ps.shopId)
UNION
(SELECT pb.id as id,
        pb.name as brandName,
        s.name as  shopName,
        ps.shopIdOrigin as shopIdOrigin, ps.shopIdDestination AS shopDestination from ProductBrand pb
                                                  join Product p on pb.id=p.productBrandId
                                                  join  ProductHasShopDestination ps on p.id =ps.productId and p.productVariantId=ps.productVariantId
                                                  join Shop s on ps.shopIdOrigin=s.id
 WHERE ps.shopIdDestination = '.$shopId.' and ps.shopIdOrigin <> '.$shopId.' group by pb.name, shopIdOrigin)', []) -> fetchAll();

        foreach ($res as $result) {
            $selectBrands[] = ['id' => $result['id'], 'brandName' => $result['brandName'], 'shopName' => $result['shopName'], 'shopIdOrigin' => $result['shopIdOrigin'], 'shopIdDestination' => $result['shopIdDestination']];
        }

        return json_encode($selectBrands);
    }
}