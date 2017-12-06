<?php
namespace bamboo\controllers\back\ajax;
use bamboo\domain\entities\CProduct;
use bamboo\domain\entities\CProductSku;
use bamboo\domain\repositories\CProductRepo;

/**
 * Class CDeleteProduct
 * @package bamboo\blueseal\controllers\ajax
 *
 * @author Bambooshoot Team <emanuele@bambooshoot.agency>, ${DATE}
 *
 * @copyright (c) Bambooshoot snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @since ${VERSION}
 */
class CViewSizeProduct extends AAjaxController
{
    /**
     * @return string
     */
    public function post()
    {
        $rowId = $_POST['passId'];
        $rowVariant = $_POST['passVariant'];

        $sql =  "SELECT p.id, ps.name
                 FROM ProductSize ps
                 JOIN ProductSku pk ON ps.id = pk.productSizeId
                 JOIN Product p ON pk.productId = p.id AND pk.productVariantId = p.productVariantId
                 WHERE p.id = ? AND p.productVariantId = ?
                 GROUP BY p.id";

        //$returnSize = \Monkey::app()->repoFactory->create('Product')->findOneBySql($sql, [$rowId, $rowVariant]);

        /** @var CProductSku $returnSize */
        $returnSize = \Monkey::app()->repoFactory->create('ProductSku')->findBy(['productId' => $rowId, 'productVariantId' => $rowVariant ]);
        $val = $returnSize->productSize->name;
        echo $val;


    }
}