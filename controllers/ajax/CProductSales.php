<?php
namespace bamboo\blueseal\controllers\ajax;
use bamboo\core\exceptions\BambooException;

/**
 * Class CProductListAjaxController
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
class CProductSales extends AAjaxController
{
    public function get()
    {

    }

    public function post()
    {
        $get = $this->app->router->request()->getRequestData();
        $action = '';
        if (array_key_exists('action', $get)) $action = $get['action'];

        switch ($action) {
            case 'assign':
                $res = $this->assign($get['rows'], $get['isSale'], $get['percentage']);
                break;
            default:
                $res = "Nessuna azione è stata selezionata";
        }
        return $res;
    }

    private function assign($rows, $isSale, $percent = 0)
    {
        if ($isSale && !$percent) return "Non è stata specificata la percentuale di sconto";
        $ids = [];
        $varIds = [];

        if (!$isSale) {
            foreach ($rows as $v) {
                $ids[] = $v['id'];
                $varIds[] = $v['productVariantId'];
            }
            $ids = implode(',', $ids);
            $varIds = implode(',', $varIds);

            $sql = "UPDATE ProductSku SET salePrice = 0, isOnsale = 0 WHERE productId IN (" . $ids . ") AND productVariantId IN (" . $varIds . ")";
            try {
                $res = $this->app->dbAdapter->query($sql, []);
            } catch (\Exception $e) {
                return "Non riesco a rimuovere le promozioni dai prodotti selezionati:<br />" . $e->getMessage();
            }
            return "I prodotti selezionati non sono più in sconto";
        }

        if ($isSale) {
            foreach ($rows as $v) {
                $this->app->dbAdapter->beginTransaction();
                try {
                    $sql = "UPDATE ProductSku SET salePrice = FLOOR(price / 100 * (100 - " . $percent . ")), isOnsale = 1 WHERE productId = " . $v['id'] . " AND productVariantId = (" . $v['productVariantId'] . ")";
                    $res = $this->app->dbAdapter->query($sql, []);
                } catch (\Exception $e) {
                    $this->app->dbAdapter->rollback();
                    return "Non riesco ad avviare le promozioni le promozioni dai prodotti selezionati:<br />" . $e->getMessage();
                }
            }
            $this->app->dbAdapter->commit();
            return "Promozioni aggiunte e aggiornate!";
        }
    }
}