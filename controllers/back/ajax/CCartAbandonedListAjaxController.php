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

class CCartAbandonedListAjaxController extends AAjaxController
{

    public function get()
    {
        $sql = "SELECT
  C.id                                                   AS id,
  C.creationDate                                         AS creationDate,
  U.email                                                AS email,
  C.cartTypeId                                           AS carTypeId,
  C.lastUpdate                                           AS lastUpdate
FROM Cart C
  INNER JOIN User U ON C.userId = U.id
  INNER JOIN CartLine Cl ON C.id = Cl.cartId
  INNER JOIN ProductPublicSku pps ON Cl.productId = pps.productId AND Cl.productVariantId = pps.productVariantId
  INNER JOIN Product p ON Cl.productId = p.id AND Cl.productVariantId = p.productVariantId

WHERE C.userId <> ''
      AND C.cartTypeId = 1
GROUP BY C.id";

        $datatable = new CDataTables($sql, ['id'], $_GET, true);

        $datatable->doAllTheThings(true);

        foreach ($datatable->getResponseSetData() as $key => $row) {


            $row['creationDate'] = STimeToolbox::FormatDateFromDBValue($row['creationDate'], 'd-m-Y H:i:s');
            $row['lastUpdate'] = STimeToolbox::FormatDateFromDBValue($row['lastUpdate'], 'd-m-Y H:i:s');
            /* @var  $cartsLine CObjectCollection */
            $cartsLine = \Monkey::app()->repoFactory->create('CartLine')->findBy(['cartId' => $row['id']]);
            $row['price'] = 0;

            foreach ($cartsLine as $cartLine) {
                $productId = $cartLine->productId;
                $productVariantId = $cartLine->productVariantId;
                $productSizeId = $cartLine->productSizeId;
                $isOnSaleCheck = \Monkey::app()->repoFactory->create('Product')->findOneBy(['id' => $productId, 'productVariantId' => $productVariantId]);
                $isOnSale = $isOnSaleCheck->isOnSale;
                if ($isOnSale == "1") {
                    $priceFind = \Monkey::app()->repoFactory->create('ProductPublicSku')->findOneBY(['productId' => $productId, 'productVariantId' => $productVariantId, 'productSizeId' => $productSizeId]);
                    $price = $priceFind->saleprice;
                    $row['price'] = $row['price'] + $price;
                } else {
                    $priceFind = \Monkey::app()->repoFactory->create('ProductPublicSku')->findOneBY(['productId' => $productId, 'productVariantId' => $productVariantId, 'productSizeId' => $productSizeId]);
                    $price = $priceFind->price;
                    $row['price'] = $row['price'] + $price;

                }

            }


            $row['price'] = "<span class=\"label label-warning\">" . SPriceToolbox::formatToEur($row['price'], true) . "</span>";


            $datatable->setResponseDataSetRow($key, $row);


        }

        return $datatable->responseOut();
    }


}