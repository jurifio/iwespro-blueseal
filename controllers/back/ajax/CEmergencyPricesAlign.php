<?php
namespace bamboo\controllers\back\ajax;

/**
 * Class CEmergencyPricesAlign
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 30/12/2017
 * @since 1.0
 */
class CEmergencyPricesAlign extends AAjaxController
{

    public function post()
    {
        $sql = "SELECT
                  count(1) as conto
                FROM ProductSku ps1
                  JOIN (SELECT
                          p.id,
                          p.productVariantId,
                          p.isOnSale as onSale,
                          count(DISTINCT ps.salePrice) AS prezziSaldo,
                          count(DISTINCT ps.price) AS prezzi
                        FROM Product p
                          JOIN ProductSku ps ON p.id = ps.productId AND p.productVariantId = ps.productVariantId
                        WHERE p.qty > 0
                        GROUP BY p.id, p.productVariantId
                        HAVING prezzi > 1 or prezziSaldo > 1
                       ) q1 ON ps1.productId = q1.id AND ps1.productVariantId = q1.productVariantId ";
        $res = \Monkey::app()->dbAdapter->query($sql,[])->fetchAll();

        if($res[0]['conto'] > 0) {
            \Monkey::app()->router->response()->raiseProcessingError();
            return 'Non puoi allineare i prezzi sugli sku pubblici se sono disallineati negli sku, SVUOTA LA TABELLA';
        }

        $sql = "UPDATE ProductPublicSku ppsk
                 JOIN Product p ON (ppsk.productId, ppsk.productVariantId) = (p.id, p.productVariantId)
                 JOIN ProductSku psk ON (p.id, p.productVariantId) = (psk.productId, psk.productVariantId)
                  SET ppsk.salePrice = psk.salePrice, ppsk.price = psk.price
                WHERE p.qty > 0 and (psk.salePrice <> ppsk.salePrice or psk.price <> ppsk.price)";
        return 'Sono state aggoiornate: '.\Monkey::app()->dbAdapter->query($sql,[])->countAffectedRows().' righe';
    }
}