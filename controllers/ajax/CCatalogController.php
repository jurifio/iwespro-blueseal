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
        try {
            $search = $this->app->router->request()->getRequestData('search');
            $magShop = $this->app->router->request()->getRequestData('shop');
            $type = '';
            if (false !== strpos($search, '-')) $type = 'code';
            elseif (false !== strpos($search, '#')) $type = 'cpf';
            elseif ((0 === strpos($search, '12')) && (10 == strlen($search))) $type = 'barcode';

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
                    $prodVariant = $variantRepo->findBy(['name' => $variant]);
                    $prodVar = [];
                    foreach ($prodVariant as $pv) {
                        $prod = $prodRepo->findOneBy(['itemno' => $itemno, 'productVariantId' => $pv->id]);
                        if ($prod) break;
                    }
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
        } catch(\Exception $e) {
            return json_encode($e->getMessage());
        }

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

        $arrRet['value'] = '';
        $arrRet['price'] = '';
        $sku = $this->rfc('ProductSku')->findOneBy(['shopId' => $shopId, 'productVariantId' => $em->productVariantId]);
        if ($sku) {
            $arrRet['value'] = $sku->value;
            $arrRet['price'] = $sku->price;
        } else {
            $shp = $this->rfc('ShopHasProduct')->findOneBy(['shopId' => $shopId, 'productVariantId' => $em->productVariantId]);
            if ($shp) {
                $arrRet['value'] = $shp->value;
                $arrRet['price'] = $shp->price;
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
            $SOEm = $this->rfc('StorehouseOperation');
            $SOLRepo = $this->rfc('StorehouseOperationLine');
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

            $this->app->dbAdapter->beginTransaction();

            if (!$storehouse) {
                $storehouse = $SEm->getEmptyEntity();
                $storehouse->shopId = $shop->id;
                $storehouse->name = 'auto-generated';
                $storehouse->countryId = 1;
                $storehouse->id = $storehouse->insert();
            }

            if (!$SOC = $SOCEm->findOne([$get['mag-movementCause']])) throw new \Exception('La causale è obbligatoria');

            //fatti tutti i controlli preliminari, inizio la transazione


            $newOp = $SOEm->getEmptyEntity();
            $newOp->shopId = $shop->id;
            $newOp->storehouseId = $storehouse->id;
            $newOp->storehouseOperationCauseId = $get['mag-movementCause'];
            $newOp->userId = $user->id;
            $newOp->operationDate = date("Y-m-d H:i:s", strtotime($get['mag-movementDate']));
            $newOp->id = $newOp->insert();

            //inizio l'inserimento dei singoli movimenti
            foreach ($moves as $v) {
                $SOLRepo->createMovementLine($v['id'], $v['productVariantId'], $v['productSizeId'], $shop->id, $v['qtMove'], $newOp->id, $storehouse->id);
            }

            $this->app->dbAdapter->commit();
            return json_encode('OK');
        } catch (\Exception $e) {
            $this->app->dbAdapter->rollBack();
            return json_encode($e->getMessage());
        }
    }
}