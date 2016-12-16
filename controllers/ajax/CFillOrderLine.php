<?php


namespace bamboo\blueseal\controllers\ajax;

use bamboo\ecommerce\views\widget\VBase;
use bamboo\blueseal\business\COrderLineManager;
use bamboo\core\base\CObjectCollection;
use bamboo\core\theming\CRestrictedAccessWidgetHelper;
use bamboo\core\db\pandaorm\repositories\CRepo;
use bamboo\core\ecommerce\IBillingLogic;

class CFillOrderLine extends AAjaxController
{
    public function get()
    {
        try{
            $view = new VBase(array());
            $view->setTemplatePath($this->app->rootPath().$this->app->cfg()->fetch('paths','blueseal').'/template/widgets/orderLine.php');
            $filters = $this->data;

            /** @var CRepo $repo */
            $repo = $this->app->repoFactory->create('OrderLine');
            $line = $repo->findOne(explode('-',$filters['order']));

            $lineManager = new COrderLineManager($this->app,$line);

            $repo = $this->app->repoFactory->create('Product');
            $line->product = $repo->findOne(array($line->productId,$line->productVariantId));

            $size = $this->app->dbAdapter->query("select `name` from ProductSize where id = ? ", [$line->productSizeId] )->fetchAll()[0];
            $line->productSize = $size['name'];

            $line->skus = new CObjectCollection();
            $line->skus->add( $lineManager->getSelectedSku());

            if($lineManager->isFriendChangable()){
                $line->skus->addAll($lineManager->getAlternativesSkus());
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
                'line' => $line,
                'lineManager' => $lineManager
            ]);

        }catch (\Throwable $e){
            var_dump($e);
            return 'error';
        }

    }
}