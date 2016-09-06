<?php
namespace bamboo\blueseal\controllers\ajax;

use bamboo\core\intl\CLang;
use bamboo\core\theming\CRestrictedAccessWidgetHelper;
use bamboo\ecommerce\views\widget\VBase;

/**
 * Class CUserSellRecapController
 * @package bamboo\blueseal\controllers\ajax
 *
 * @author Bambooshoot Team <emanuele@bambooshoot.agency>
 *
 * @copyright (c) Bambooshoot snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 2016/04/08
 * @since 1.0
 */
class CCatalogController extends AAjaxController
{
    protected $urls = [];
    protected $authorizedShops = [];
    protected $em;

    /**
     * @param $action
     * @return mixed
     */
    public function createAction($action)
    {
        $this->app->setLang(new CLang(1, 'it'));
        $this->urls['base'] = $this->app->baseUrl(false) . "/blueseal/";
        $this->urls['page'] = $this->urls['base'] . "prodotti/movimenti/inserisci";
        $this->urls['dummy'] = $this->app->cfg()->fetch('paths', 'dummyUrl');

        $this->em = new \stdClass();
        $this->em->products = $this->app->entityManagerFactory->create('Product');

        return $this->{$action}();
    }

    public function get()
    {
        $search = $this->app->router->request()->getRequestData('search');
        $magShop = $this->app->router->request()->getRequestData('shop');
        $type = '';
        if (false !== strpos($search, '-')) $type = 'code';
        elseif (false !== strpos($search, '#')) $type = 'cpf';
        elseif ((0 == strpos($search, '12')) && (10 == strlen($search))) $type = 'barcode';

        $allShops = $this->app->getUser()->hasPermission('allShops');

        if ($allShops) {
            if ('' == $magShop) throw new \Exception('Non hai specificato lo shop');
            $shopId = $magShop;
        } else {
            $shop = $this->app->getUser()->shop;
            foreach ($shop as $v) {
                $shopId = $v->id;
            }
        }

        $skuRepo = $this->rfc('ProductSku');
        $variantRepo = $this->rfc('productVariant');
        $prodRepo = $this->rfc('Product');
        $sizesToMove = [];

        switch ($type) {
            case 'code':
                $prod = $prodRepo->findOneByStringId($search);
                break;
            case 'cpf':
                list($itemno, $variant) = explode('#', $search);
                $itemno = trim($itemno);
                $variant = trim($variant);
                $prodVariant = $variantRepo->findOneBy(['name' => $variant]);
                $prod = $prodRepo->findOneBy(['itemno' => $itemno, 'productVariantId' => $prodVariant->id]);
                break;
            case 'barcode':
                $sku = $skuRepo->findOneBy(['barcode' => $search]);
                $sizesToMove[$sku->productSizeId] = 1;
                $prod = $sku->product;
                break;
            default:
                return json_encode(false);
        }
        $ret = ($prod) ? $this->getAllProductData($prod, $shopId, $sizesToMove) : false;

        return json_encode($ret);
    }

    private function getAllProductData($em, $shopId, $sizesToMove = [])
    {
        $arrRet = $em->toArray();
        $arrRet['productVariantName'] = $em->productVariant->name;
        $arrRet['sizes'] = [];
        $arrRet['sku'] = [];
        foreach ($em->productSizeGroup->productSize as $v) {
            $arrRet['sizes'][$v->id] = $v->name;
        }
        foreach ($em->productSku as $v) {
            if (($v->stockQty) && ($shopId == $v->shopId)) {
                $arrRet['sku'][$v->productSizeId] = $v->stockQty;
            }
        }
        $arrRet['moves'] = [];
        foreach ($sizesToMove as $k => $v) {
            $arrRet['moves'][$k] = $v;
        }
        return $arrRet;
    }

    public function post()
    {
        $get = $this->app->router->request()->getRequestData();

        try {
            $prodEm = $this->rfc('Product');
            $skuEm = $this->rfc('ProductSku');
            $SOEm = $this->rfc('StorehouseOperation');
            $SOLEm = $this->rfc('StorehouseOperationLine');
            $SOCEm = $this->rfc('StorehouseOperationCause');
            $SEm = $this->rfc('Storehouse');

            $moves = [];
            $i = 0;

            //create array with all movements
            foreach ($get as $gk => $gv) {
                if ((0 === strpos($gk, 'move')) && ('' !== $gv)) {
                    $tempArr = explode('-', $gk);
                    $moves[$i]['id'] = $tempArr[1];
                    $moves[$i]['productVariantId'] = $tempArr[2];
                    $moves[$i]['productSizeId'] = $tempArr[3];
                    $moves[$i]['qtMove'] = $gv;
                    $i++;
                }
            }
            unset($tempArr);

            $user = $this->app->getUser();
            if ($user->hasPermission('allShops')) {
                if (!$get['mag-shop']) throw new \Exception('Lo shop deve essere specificato obbligatoriamente');
                $shop = $this->rfc('Shop')->findOneBy(['id' => $get['mag-shop']]);
            } else {
                $shop = $user->shop->getFirst();
            }

            if (isset($get['storehouseId'])) {
                $storehouse = $SEm->findOneBy(['id' => $get['storehouseId'],'shopId' => $shop->id]);
            } else {
                $storehouse = $SEm->findOneBy(['shopId' => $shop->id]);
            }

            if (!$storehouse) {
                $storehouse = $SEm->getEmptyEntity();
                $storehouse->shopId = $shop->id;
                $storehouse->name = 'auto-generated';
                $storehouse->countryId = 1;
                $storehouse->id = $storehouse->insert();
            }
            if (!$SOC = $SOCEm->findOne([$get['mag-movementCause']])) throw new \Exception('La causale è obbligatoria');


            //fatti tutti i controlli preliminari, inizio la transazione
            $this->app->dbAdapter->beginTransaction();

            $newOp = $SOEm->getEmptyEntity();
            $newOp->shopId = $shop->id;
            $newOp->storehouseId = $storehouse->id;
            $newOp->storehouseOperationCauseId = $get['mag-movementCause'];
            $newOp->userId = $user->id;
            $newOp->operationDate = date("Y-m-d H:i:s", strtotime($get['mag-movementDate']));
            $operationId = $newOp->insert();

            //inizio l'inserimento dei singoli movimenti
            foreach ($moves as $v) {
                $actualProd = $prodEm->findOneBy([
                    'id' => $v['id'],
                    'productVariantId' => $v['productVariantId']
                ]);
                if (!$actualProd) throw new \Exception("Uno o più Prodotti non sono stati trovati");

                //recupero i prezzi del prodotto
                $allSkus = $skuEm->findBy([
                    'productVariantId' => $v['productVariantId'],
                    'productId' => $v['id'],
                ]);
                $value = 0;
                $price = 0;
                $salePrice = 0;
                $onSale = null;
                $isUsable = null;
                $i = 0;

                foreach ($allSkus as $s) {
                    $isUsable = true;
                    if (0 == $i) {
                        $value = $s->value;
                        $price = $s->price;
                        $salePrice = $s->salePrice;
                        $onSale = $s->isOnSale;
                    } elseif (
                        ($price != $s->price) ||
                        ($salePrice != $s->salePrice) ||
                        ($onSale != $s->isOnSale)
                    ) {
                        $isUsable = false;
                        break;
                    }
                    $i++;
                }
                if (false === $isUsable) {
                    $shp = $actualProd->shopHasProduct->findOneByKeys([
                        'productVariantId' => $v['productVariantId'],
                        'productId' => $v['id'],
                        'shopId' => $shop->id,
                    ]);
                    if ($shp) {
                        $price = $shp->price;
                        $value = $shp->value;
                        $salePrice = $shp->salePrice;
                    } else {
                        throw new \Exception("Il prezzo di uno o più prodotti in elenco non è stato impostato. I movimenti non sono stati registrati");
                    }
                }

                //modifico le quantità negli sku
                $actualSku = $allSkus->findOneByKeys([
                    'productVariantId' => $v['productVariantId'],
                    'shopId' => $shop->id,
                    'productSizeId' => $v['productSizeId']
                ]);
                if ($actualSku) {
                    if (0 > $actualSku->stockQty + $v['qtMove']) throw new \Exception('I movimenti non possono portare le quantità in stock in negativo');
                    $actualSku->stockQty = $actualSku->stockQty + $v['qtMove'];
                    $actualSku->update();
                } else {
                    if (0 > $v['qtMove']) throw new \Exception(
                        'Impossibile togliere quantità di un articolo mai caricato: ' . $v['id'] . '-' . $v['productVariantId']
                    );
                    $newSku = $skuEm->getEmptyEntity();
                    $newSku->productId = $v['id'];
                    $newSku->productVariantId = $v['productVariantId'];
                    $newSku->productSizeId = $v['productSizeId'];
                    $newSku->shopId = $shop->id;
                    $newSku->currencyId = 1;
                    $newSku->value = $value;
                    $newSku->price = $price;
                    $newSku->salePrice = $salePrice;
                    $newSku->isOnSale = (null === $onSale) ? 0 : $onSale;
                    $newSku->insert();
                }

                //inserisco il movimento
                $SOL = $SOLEm->getEmptyEntity();
                $SOL->storehouseOperationId = $operationId;
                $SOL->shopId = $shop->id;
                $SOL->storehouseId = $storehouse->id;
                $SOL->productId = $v['id'];
                $SOL->productVariantId = $v['productVariantId'];
                $SOL->productSizeId = $v['productSizeId'];
                $SOL->qty = $v['qtMove'];
                $SOL->insert();
                $this->app->dbAdapter->commit();
            }
            return 'OK';
        } catch (\Exception $e) {
            $this->app->dbAdapter->rollBack();
            var_dump($e);
            return $e->getMessage();
        }
    }
}