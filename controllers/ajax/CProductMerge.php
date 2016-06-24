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
                if ($prod->productSizeGroupId !== $sizeGroup) {
                    $sizeGroupCompatibility = false;
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
        $get = $this->app->router->request()->getRequestData();
        $action = '';
        if (array_key_exists('action', $get)) $action = $get['action'];

        switch ($action) {
            case 'merge': {
                $res = $this->mergeProducts($get['rows'], $get['choosen']);
                break;
            }
        }
        return $res;
    }

    private function mergeProducts($rows, $choosen)
    {
        //controllo che i prodotti non scelti per la fusione non abbiano ordini

        foreach ($rows as $k => $v) {
            if ($choosen != $k) {
                $ol = $this->app->repoFactory->create('OrderLine')->findBy(['productId' => $v['id'], 'productVariantId' => $v['productVariantId']]);
                if ($ol->count()) {
                    return "ERRORE: Il prodotto da fondere non può contenere ordini!";
                }
            }
        }

        //controllo di nuovo i gruppi taglie
        $sizeGroup = 0;
        foreach ($rows as $k => $v) {
            $prod = $this->app->repoFactory->create('Product')->findOneBy(['Id' => $v['id'], 'productVariantId' => $v['productVariantId']]);
            if (0 == $sizeGroup) $sizeGroup = $prod->productSizeGroupId;
            elseif ($sizeGroup != $prod->productSizeGroupId) return "ERRORE: Il prodotto da fondere non può contenere ordini!";
        }

        //controllo che nessuno dei prodotti siano già fusi.
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
                    $sop = $this->app->repoFactory->create('ShopHasProduct')->findOneBy(['productId' => $v['id'], 'productVariantId' => $v['productVariantId']]);
                    $sop->productId = $rows[$choosen]['id'];
                    $sop->productVariantId = $rows[$choosen]['productVariantId'];
                    $sop->update();
                } catch (\Exception $e) {
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
                $dp = $this->app->repoFactory->create('DirtyProduct')->findOneBy(['productId' => $v['id'], 'productVariantId' => $v['productVariantId']]);
                $dp->productId = $rows[$choosen]['id'];
                $dp->productVariantId = $rows[$choosen]['productVariantId'];
                $dp->update();
            } catch(\Exception $e) {
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
                $ps = $this->app->repoFactory->create('ProductSku')->findOneBy(['productId' => $v['id'], 'productVariantId' => $v['productVariantId']]);
                $ps->productId = $rows[$choosen]['id'];
                $ps->productVariantId = $rows[$choosen]['productVariantId'];
                $ps->update();
            } catch(\Exception $e) {
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
            } catch(\Exception $e) {
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
        } catch (\Exception $e) {
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