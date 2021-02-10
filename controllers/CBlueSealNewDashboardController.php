<?php

namespace bamboo\blueseal\controllers;

use bamboo\ecommerce\views\VBase;
use bamboo\core\theming\CRestrictedAccessWidgetHelper;

/**
 * Class CBlueSealDashboardController
 * @package bamboo\blueseal\controllers
 *
 * @author Bambooshoot Team <emanuele@bambooshoot.agency>, ${DATE}
 *
 * @copyright (c) Bambooshoot snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @since ${VERSION}
 */
class CBlueSealNewDashboardController extends ARestrictedAccessRootController
{
    protected $fallBack = "home";
    protected $logFallBack = "blueseal";
    protected $pageSlug = "newdashboard";

    public function get()
    {
        $data = \Monkey::app()->router->request()->getRequestData();
        $today = (new \DateTime())->format('Y-m-d H:i:s');
        $currentMonth = (new \DateTime())->format('m');
        $currentyear = (new \DateTime())->format('Y');
        $yesterday = (new \DateTime())->modify("-1 day")->format('Y-m-d H:i:s');
        $lastMonth = (new \DateTime())->modify("-1 month")->format('m');
        $lastYear = (new \DateTime())->modify("-1 year")->format('Y');


        $stats = [];
        $sqlOrder = 'select sum(o.netTotal) as totalOrder,count(o.id) as countOrder from `Order` `o`
WHERE `o`.`status` LIKE \'ORD_SHIPPED\' OR `o`.`status` LIKE \'ORD_DELIVERED\' OR  `o`.`status` LIKE \'ORD_CLOSED\'   and o.paymentDate is not null';
        $resOrder = \Monkey::app()->dbAdapter->query($sqlOrder,[])->fetchAll();
        foreach ($resOrder as $orderData) {
            $totalOrder = $orderData['totalOrder'];
            $quantityOrder = $orderData['countOrder'];

        }
        $sqlOrderReturn = 'select sum(o.netTotal) as totalOrder,count(o.id) as countOrder from `Order` `o`
 WHERE `o`.`status` LIKE \'ORD_RETURNED\'';
        $resOrderReturn = \Monkey::app()->dbAdapter->query($sqlOrderReturn,[])->fetchAll();
        foreach ($resOrderReturn as $orderDataReturn) {
            $totalOrderReturn = $orderDataReturn['totalOrder'];
            $quantityOrderReturn = $orderDataReturn['countOrder'];
        }
        $cartTotalNumber = 'select count(*) AS totalCart from UserSessionHasCart us join  `Cart` c on  `us`.cartId=c.id';
        $resCartTotalNumber = \Monkey::app()->dbAdapter->query($cartTotalNumber,[])->fetchAll();
        foreach ($resCartTotalNumber as $cartTotalNumber) {
            $cartTotalNumber = $cartTotalNumber['totalCart'];
        }


        $cartTotalValue = 0;
        $carts = \Monkey::app()->repoFactory->create('UserSessionHasCart')->findAll();
        foreach ($carts as $cart) {
            $cartLines = \Monkey::app()->repoFactory->create('CartLine')->findBy(['cartId' => $cart->cartId]);
            $order = \Monkey::app()->repoFactory->create('Order')->findOneBy(['cartId' => $cart->cartId]);
            if ($order) {
                foreach ($cartLines as $cartLine) {
                    $productSku = \Monkey::app()->repoFactory->create('ProductSku')->findOneBy(['productId' => $cartLine->productId,'productVariantId' => $cartLine->productVariantId,'productSizeId' => $cartLine->productSizeId]);
                    $product = \Monkey::app()->repoFactory->create('Product')->findOneBy(['id' => $cartLine->productId,'productVariantId' => $cartLine->productVariantId]);
                    if($productSku) {
                        if ($product->isOnSale == 1) {
                            $cartTotalValue += $productSku->salePrice;
                        } else {
                            $cartTotalValue += $productSku->price;
                        }
                    }else{
                        $cartTotalValue=$cartTotalValue;
                    }

                }
            } else {
                continue;
            }
        }
        $cartAbandonedTotalNumber = 'select count(c.id) AS totalCart from Cart c left join UserSessionHasCart us on c.id=us.cartId where us.cartId is null';
        $resCartAbandonedTotalNumber = \Monkey::app()->dbAdapter->query($cartAbandonedTotalNumber,[])->fetchAll();
        foreach ($resCartAbandonedTotalNumber as $resCartAbandoned) {
            $cartAbandonedTotal = $resCartAbandoned['totalCart'];
        }
        $cartAbandonedTotalValue = 0;
        $cartAbandonedValue = 'select c.id AS cartId from Cart c left join UserSessionHasCart us on c.id=us.cartId where us.cartId is null';
        $resCartAbandonedValue = \Monkey::app()->dbAdapter->query($cartAbandonedValue,[])->fetchAll();
        foreach ($resCartAbandonedValue as $cart) {
            $cartLines = \Monkey::app()->repoFactory->create('CartLine')->findBy(['cartId' => $cart['cartId']]);
            $order = \Monkey::app()->repoFactory->create('Order')->findOneBy(['cartId' => $cart['cartId']]);
            if ($order) {
                foreach ($cartLines as $cartLine) {
                    $productSku = \Monkey::app()->repoFactory->create('ProductSku')->findOneBy(['productId' => $cartLine->productId,'productVariantId' => $cartLine->productVariantId,'productSizeId' => $cartLine->productSizeId]);
                    $product = \Monkey::app()->repoFactory->create('Product')->findOneBy(['id' => $cartLine->productId,'productVariantId' => $cartLine->productVariantId]);
                   if($productSku) {
                       if ($product->isOnSale == 1) {
                           $cartAbandonedTotalValue += $productSku->salePrice;
                       } else {
                           $cartAbandonedTotalValue += $productSku->price;
                       }
                   }else{
                       $cartAbandonedTotalValue=$cartAbandonedTotalValue;
                   }

                }
            } else {
                continue;
            }
        }

        $sqlTotalUser = 'select count(*) as countUser from `User` where isActive=1';
        $resCountUser = \Monkey::app()->dbAdapter->query($sqlTotalUser,[])->fetchAll();
        foreach ($resCountUser as $countUser) {
            $totalUser = $countUser['countUser'];
        }
        $sqlTotalUserOnline = "select count(*) as countUser from `UserSession`  where creationDate between >='" . $today . "' and '" . $yesterday . "'";
        $resCountUser = \Monkey::app()->dbAdapter->query($sqlTotalUserOnline,[])->fetchAll();
        foreach ($resCountUser as $countUser) {
            $totalUserOnline = $countUser['countUser'];
        }
        $sqlTotalProduct = 'select count(*) as totalProduct from Product where productStatusId=6 and qty>0';
        $resTotalProduct = \Monkey::app()->dbAdapter->query($sqlTotalProduct,[])->fetchAll();
        foreach ($resTotalProduct as $product) {
            $totalProduct = $product['totalProduct'];
        }

        $stats[] = ['totalOrder' => $totalOrder,
            'quantityOrder' => $quantityOrder,
            'totalOrderReturn' => $totalOrderReturn,
            'quantityOrderReturn' => $quantityOrderReturn,
            'cartTotalNumber' => $cartTotalNumber,
            'cartTotalValue' => $cartTotalValue,
            'cartAbandonedTotal'=>$cartAbandonedTotal,
            'cartAbandonedTotalValue'=>$cartAbandonedTotalValue,
            'totalUser'=>$totalUser,
            'totalUserOnline'=>$totalUserOnline,
            'totalProduct'=>$totalProduct
        ];




        $view = new VBase(array());
        $view->setTemplatePath($this->app->rootPath() . $this->app->cfg()->fetch('paths','blueseal') . '/template/newdashboard.php');

        return $view->render([
            'app' => new CRestrictedAccessWidgetHelper($this->app),
            'data' => $this->request->getUrlPath(),
            'page' => $this->page,
            'stats' => $stats,
            'sidebar' => $this->sidebar->build()
        ]);
    }
}