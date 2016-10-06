<?php
namespace bamboo\blueseal\controllers\ajax;

use bamboo\ecommerce\views\VBase;
use bamboo\blueseal\business\CBlueSealPage;
use bamboo\core\theming\CRestrictedAccessWidgetHelper;

/**
 * Class CChangeOrderStatus
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
class COrderAjaxController extends AAjaxController
{
    protected $fallBack = "blueseal";
    protected $pageSlug = "order_print";
    protected $page;

    public function get()
    {
        $view = new VBase(array());
        $this->page = new CBlueSealPage($this->pageSlug, $this->app);
        $view->setTemplatePath($this->app->rootPath().$this->app->cfg()->fetch('paths', 'blueseal') . '/template/order_print.php');

        $orderId = $this->app->router->request()->getRequestData('orderId');

        $orderRepo = $this->app->repoFactory->create('Order');
        $order = $orderRepo->findOneBy(['id' => $orderId]);
        $repoStatus = $this->app->repoFactory->create('OrderStatus');
        $statuses = $repoStatus->findAll();
        foreach ($statuses as $status) {
            if ($status->code == $order->status) {
                $orderStatus = $status->title;
            }
        }
        $repoStatusLine = $this->app->repoFactory->create('OrderLineStatus');
        $statusesLine = $repoStatusLine->findAll();

        $userAddress = igbinary_unserialize($order->frozenBillingAddress);
        $userAddress->setEntityManager($this->app->entityManagerFactory->create('UserAddress'));
        $userShipping = igbinary_unserialize($order->frozenShippingAddress);
        $userShipping->setEntityManager($this->app->entityManagerFactory->create('UserAddress'));

        $productRepo = $this->app->repoFactory->create('ProductNameTranslation');

        return $view->render([
            'app' => new CRestrictedAccessWidgetHelper($this->app),
            'userAddress' => $userAddress,
            'userShipping' => $userShipping,
            'order' => $order,
            'orderStatus' => $orderStatus,
            'statusesLine' => $statusesLine,
            'productRepo' => $productRepo,
            'page' => $this->page,
            'logo' => $this->app->cfg()->fetch("miscellaneous", "logo"),
            'fiscalData' => $this->app->cfg()->fetch("miscellaneous", "fiscalData")
        ]);

        }

}

