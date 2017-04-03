<?php
namespace bamboo\controllers\back\ajax;

use bamboo\domain\repositories\CMarketplaceAccountHasProductRepo;

/**
 * Class CMarketplaceProductManageController
 * @package bamboo\blueseal\controllers\ajax
 *
 * @author Bambooshoot Team <emanuele@bambooshoot.agency>
 *
 * @copyright (c) Bambooshoot snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 18/07/2016
 * @since 1.0
 */
class CMarketplaceCategoryProductManageController extends AAjaxController
{
    /**
     * @return int
     */
    public function delete()
    {
        $ids = $this->app->router->request()->getRequestData('categories');
        $marketplaceAccountIds = $this->app->router->request()->getRequestData('marketplaceAccountId');
        $marketplaceAccount = $this->app->repoFactory->create('MarketplaceAccount')->findOneByStringId($marketplaceAccountIds);
        /** @var CMarketplaceAccountHasProductRepo $mahpRepo */
        $mahpRepo = $this->app->repoFactory->create('MarketplaceAccountHasProduct');
        $ok = 0;
        $ko = 0;

        foreach ($ids as $id) {
            $sql = "SELECT distinct p.id as productId, p.productVariantId  
                FROM Product p 
                    JOIN ProductStatus ps ON p.productStatusId = ps.id 
                    JOIN ProductHasProductCategory phpc ON p.id = phpc.productId AND 
                                                           p.productVariantId = phpc.productVariantId
                    JOIN ProductCategory pcN on pcN.id = phpc.productCategoryId
                    JOIN ProductCategory pcF on pcN.lft BETWEEN pcF.lft and pcF.rght
                WHERE ps.isVisible = 1 and pcF.id = ?";
            foreach ($this->app->dbAdapter->query($sql,[$id])->fetchAll() as $productIds) {
                $mahp = $mahpRepo->getEmptyEntity();
                $mahp->setIds($productIds+[
                    'marketplaceAccountId' => $marketplaceAccount->id,
                    'marketplaceId' => $marketplaceAccount->marketplaceId
                    ]);
                if($mahpRepo->deleteProductFromMarketplaceAccount($mahp->printId())) $ok ++;
                else $ko ++;
            }
        }
        return json_encode(['ok'=>$ok,'ko'=>$ko]);
    }
}