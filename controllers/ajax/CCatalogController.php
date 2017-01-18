<?php
namespace bamboo\blueseal\controllers\ajax;

use bamboo\core\exceptions\BambooException;
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
        } catch (\Throwable $e) {
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
        if ($em->productSizeGroup) {
            foreach ($em->productSizeGroup->productSize as $v) {
                $arrRet['sizes'][$v->id] = $v->name;
            }
        }
        foreach ($em->productSku as $v) {
            $qty = $v->stockQty + $v->padding * -1;
            if (($qty) && ($shopId == $v->shopId)) {
                $single = [];
                $single['qty'] = $qty;
                $single['padding'] = ($v->padding < 0) ? true : false;
                $arrRet['sku'][$v->productSizeId] = $single;
            }
        }

        $arrRet['value'] = '';
        $arrRet['price'] = '';
        $shp = $this->rfc('ShopHasProduct')->findOneBy(['shopId' => $shopId, 'productVariantId' => $em->productVariantId]);

        if ($shp) {
            $arrRet['value'] = $shp->value;
            $arrRet['price'] = $shp->price;
        }

        $arrRet['moves'] = [];
        foreach ($sizesToMove as $k => $v) {
            $arrRet['moves'][$k] = $v;
        }
        return $arrRet;
    }

    /**
     * @return bool|string
     * @throws \Exception
     * @transaction
     */
    public function post()
    {
        $get = $this->app->router->request()->getRequestData();
        $rf = \Monkey::app()->repoFactory;
        $soR = $rf->create('StorehouseOperation');

        $moves = [];
        //create array with all movements
        foreach ($get as $gk => $gv) {
            $single = [];
            if ((0 === strpos($gk, 'move')) && ('' !== $gv)) {
                $tempArr = explode('-', $gk);
                $single['id'] = $tempArr[1];
                $single['productVariantId'] = $tempArr[2];
                $single['productSizeId'] = $tempArr[3];
                $single['qtMove'] = $gv;
                $moves[] = $single;
            }
        }

        unset($tempArr);
        unset($single);

        $user = $this->app->getUser();
        if ($user->hasPermission('allShops')) {
            if (!$get['mag-shop']) throw new \Exception('Lo shop deve essere specificato obbligatoriamente');
            $shop = $rf->create('Shop')->findOneBy(['id' => $get['mag-shop']]);
        } else {
            $shop = $user->shop->getFirst();
        }

        /** var CStorehouseOperationRepo */
        $dba = \Monkey::app()->dbAdapter;
        $dba->beginTransaction();
        try {
            $soR->registerOperation($moves, $shop, $get['mag-movementCause']);
            $dba->commit();
            return true;
        } catch(BambooException $e) {
            $dba->rollBack();
            return 'OOPS! Movimento non eseguito:<br /> ' . $e->getMessage();
        }
    }
}