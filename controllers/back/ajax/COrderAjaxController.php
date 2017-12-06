<?php
namespace bamboo\controllers\back\ajax;

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

        $orderRepo = \Monkey::app()->repoFactory->create('Order');
        $order = $orderRepo->findOneBy(['id' => $orderId]);
        $repoStatus = \Monkey::app()->repoFactory->create('OrderStatus');
        $statuses = $repoStatus->findAll();
        foreach ($statuses as $status) {
            if ($status->code == $order->status) {
                $orderStatus = $status->title;
            }
        }
        $repoStatusLine = \Monkey::app()->repoFactory->create('OrderLineStatus');
        $statusesLine = $repoStatusLine->findAll();

        $userAddress = \bamboo\domain\entities\CUserAddress::defrost($order->frozenBillingAddress);
        $userShipping = \bamboo\domain\entities\CUserAddress::defrost($order->frozenShippingAddress);

        $productRepo = \Monkey::app()->repoFactory->create('ProductNameTranslation');

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

