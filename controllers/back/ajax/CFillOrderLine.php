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
            $orderLineRepo = $this->app->repoFactory->create('OrderLine');
            /** @var COrderLine $line */
            $line = $orderLineRepo->findOne(explode('-',$filters['order']));

            $productRepo = $this->app->repoFactory->create('Product');
            $line->product = $productRepo->findOne(array($line->productId,$line->productVariantId));

            $size = $this->app->dbAdapter->query("select `name` from ProductSize where id = ? ", [$line->productSizeId] )->fetchAll()[0];
            $line->productSize = $size['name'];

            $line->skus = new CObjectCollection();
            $line->skus->add( $line->productSku);

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