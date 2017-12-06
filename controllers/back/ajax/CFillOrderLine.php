<?php


namespace bamboo\controllers\back\ajax;

use bamboo\domain\entities\COrderLine;
use bamboo\domain\repositories\COrderLineRepo;
use bamboo\ecommerce\views\widget\VBase;
use bamboo\core\base\CObjectCollection;
use bamboo\core\theming\CRestrictedAccessWidgetHelper;
use bamboo\core\ecommerce\IBillingLogic;

class CFillOrderLine extends AAjaxController
{
    public function get()
    {
        try{
            $view = new VBase(array());
            $view->setTemplatePath($this->app->rootPath().$this->app->cfg()->fetch('paths','blueseal').'/template/widgets/orderLine.php');
            $filters = $this->data;

            /** @var COrderLineRepo $repo */
            $orderLineRepo = \Monkey::app()->repoFactory->create('OrderLine');
            /** @var COrderLine $line */
            $line = $orderLineRepo->findOneByStringId($filters['order']);

            $line->skus = new CObjectCollection();

            if($line->isFriendChangable()){
                $line->skus->addAll($line->getAlternativesSkus());
            }

            $friendRev = 1000000;
            $iSku = 0;
            foreach($line->skus as $sku) {
                $pricer = $sku->shop->billingLogic;
                /** @var IBillingLogic $pricer */
                $pricer = new $pricer($this->app);
                $sku->friendRevenue = $pricer->calculateFriendReturnSku($sku);
                if ($friendRev > $sku->friendRevenue) $line->defaultSku = $iSku;
                $iSku++;
            }

            return $view->render([
                'app' => new CRestrictedAccessWidgetHelper($this->app),
                'line' => $line
            ]);

        }catch (\Throwable $e){
            var_dump($e);
            return 'error';
        }

    }
}