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
class CProductMerge extends AAjaxController
{
    public function get()
    {
        $get = $this->app->router->request()->getRequestData();
        $prods = $get['rows'];

        //controllo size group e se ci sono ordini relativi ai prodotti da unire
        $repoPro = $this->app->repoFactory->create('Product');
        $repoOrd = $this->app->repoFactory->create('OrderLine');
        $sizeGroupCompatibility = true;
        $sizeGroup = 0;

        foreach ($prods as $k => $v) {
            $prod = $repoPro->findOneBy(['id' => $v['id'], 'productVariantId' => $v['productVariantId']]);

            if (0 == $sizeGroup) {
                $sizeGroup = $prod->productSizeGroupId;
            } else {
                if ($prod->productSizeGroupId != $sizeGroup) {
                    $sizeGroupCompatibility = false;
                    break;
                }
            };
            $prods[$k]['areOrders'] = ($repoOrd->findBy(['productId' => $v['id'], 'productVariantId' => $v['productVariantId']])->count()) ? 1 : 0;
        }

        $res = [
            'sizeGroupCompatibility' => $sizeGroupCompatibility,
            'rows' => $prods
        ];
        return json_encode($res);
    }

    public function post()
    {
        $choosen = $this->app->router->request()->getRequestData('choosen');
        $rows = $this->app->router->request()->getRequestData('rows');
        $res = $this->mergeProducts($rows, $choosen);
        return $res;
    }

    private function mergeProducts($rows, $choosen)
    {
        //controllo che i prodotti non scelti per la fusione non abbiano ordini

       /* foreach ($rows as $k => $v) {
            if ($choosen != $k) {
                $ol = $this->app->repoFactory->create('OrderLine')->findBy(['productId' => $v['id'], 'productVariantId' => $v['productVariantId']]);
                if ($ol->count()) {
                    return "ERRORE: Il prodotto da fondere non può contenere ordini!";
                }
            }
        }*/

        //controllo di nuovo i gruppi taglie e i
        $sizeGroup = 0;
        foreach ($rows as $k => $v) {
            $prod = $this->app->repoFactory->create('Product')->findOneBy(['Id' => $v['id'], 'productVariantId' => $v['productVariantId']]);
            if (0 == $sizeGroup) $sizeGroup = $prod->productSizeGroupId;
            elseif ($sizeGroup != $prod->productSizeGroupId) return "ERRORE: Il prodotto da fondere non può contenere ordini!";
        }
        //controllo che i prodotti da fondere non siano dello stesso shop
        $shopControl = [];
        foreach ($rows as $k => $v) {
            $sku = $this->app->repoFactory->create('ProductSku')->findBy(['productId' => $v['id'], 'productVariantId' => $v['productVariantId']]);
            $shopSku = [];
            foreach($sku as $v) {
                $shopSku[] = $v->shopId;
            }
            $shopSku = array_unique($shopSku);

            if (!empty(array_intersect($shopSku, $shopControl))) {
                return "ERRORE: i prodotti selezionati per la fusione non possono venire dallo stesso Friend";
            } else {
            $shopControl = array_merge($shopControl, $shopSku);
            }
        }

        //controllo che nessuno dei prodotti sia già fuso.
        foreach ($rows as $k => $v) {
            $prod = $this->app->repoFactory->create('Product')->findOneBy(['Id' => $v['id'], 'productVariantId' => $v['productVariantId']]);
            if (13 == $prod->productStatusId) return "ERRORE: I prodotti da fondere non possono essere già fusi!";
        }

        //inizio la fusione

        try {
            $this->app->dbAdapter->beginTransaction();

            foreach ($rows as $k => $v) {
                if ($choosen == $k) continue;

                //assegno lo shop assegnato al prodotto da fondere al prodotto scelto
                try {
                    $sop = $this->app->repoFactory->create('ShopHasProduct')->findBy(['productId' => $v['id'], 'productVariantId' => $v['productVariantId']]);
                    foreach($sop as $kshop => $vshop) {
                        $vshop->productId = $rows[$choosen]['id'];
                        $vshop->productVariantId = $rows[$choosen]['productVariantId'];
                        $vshop->insert();
                    }
                } catch (\Throwable $e) {
                    throw new BambooException(
                        $this->buildErrorMsg(
                            "l'assegnazione del prodotto al nuovo shop non è andata a buon fine.",
                            $e->getMessage()),
                        [],
                        0,
                        $e
                    );
                }
            }

            //aggiorno la relazione tra product e dirtyProduct del prodotto da fondere
            try {

                foreach($sop as $kshop => $vshop) {
                    $dp = $this->app->repoFactory->create('DirtyProduct')->findOneBy([
                        'productId' => $v['id'],
                        'productVariantId' => $v['productVariantId'],
                        'shopId' => $vshop->shopId
                    ]);
                    if ($dp) {
                        $dp->productId = $rows[$choosen]['id'];
                        $dp->productVariantId = $rows[$choosen]['productVariantId'];
                        $dp->update();
                    }
                }
            } catch(\Throwable $e) {
                throw new BambooException(
                    $this->buildErrorMsg(
                        "l'aggiornento dell'associazione tra catalogo e importazione non ha funzionato.",
                        $e->getMessage()),
                    [],
                    0,
                    $e
                );
            }
            // sposto gli sku dei prodotti da fondere, facendoli puntare al prodotto scelto
            try {
                $psarr = $this->app->repoFactory->create('ProductSku')->findBy([
                    'productId' => $v['id'],
                    'productVariantId' => $v['productVariantId']
                    ]);
                foreach($psarr as $k => $ps) {
                    $ps->productId = $rows[$choosen]['id'];
                    $ps->productVariantId = $rows[$choosen]['productVariantId'];
                    $ps->insert();
                }
            } catch(\Throwable $e) {
                throw new BambooException(
                    $this->buildErrorMsg(
                        "Lo spostamento degli sku verso il prodotto scelto non ha funzionato.",
                        $e->getMessage()),
                    [],
                    0,
                    $e
                );
            }
            
            // il prodotto è fuso. Cambio lo stato in fuso

            // sposto gli sku dei prodotti da fondere, facendoli puntare al prodotto scelto
            try {
                $p = $this->app->repoFactory->create('Product')->findOneBy(['id' => $v['id'], 'productVariantId' => $v['productVariantId']]);
                $p->productStatusId = 13;
                $p->update();
            } catch(\Throwable $e) {
                throw new BambooException(
                    $this->buildErrorMsg(
                        "Il cambio di stato di uno dei prodotti non è stato eseguito.",
                        $e->getMessage()),
                    [],
                    0,
                    $e
                );
            }
            
            $this->app->dbAdapter->commit();
            return "Fusione eseguita!";
        } catch (\Throwable $e) {
            $this->app->dbAdapter->rollBack();
            return $e->getMessage();
        }
    }

    private function buildErrorMsg($customMsg, $exceptionMsg = '') {
        $ret = 'OOPS! La fusione non è stata compiuta a causa di un errore:<br />';
        $ret.= $customMsg;
        $ret.= ($exceptionMsg) ? '<br />' . $exceptionMsg : '';
        return $ret;
    }
}