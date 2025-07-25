<?php
namespace bamboo\blueseal\controllers;

use bamboo\core\theming\CRestrictedAccessWidgetHelper;
use bamboo\domain\entities\CMarketplaceAccountCategory;
use bamboo\ecommerce\views\VBase;

/**
 * Class CMarketplaceCategoryAssignInvertedController
 * @package bamboo\blueseal\controllers
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date $date
 * @since 1.0
 */
class CMarketplaceCategoryAssignInvertedController extends ARestrictedAccessRootController
{
    protected $fallBack = "blueseal";
    protected $pageSlug = "marketplace_category_assign_inverted";

    public function get()
    {
        $view = new VBase(array());
        $view->setTemplatePath($this->app->rootPath() . $this->app->cfg()->fetch('paths', 'blueseal') . '/template/marketplace_category_assign_inverted.php');

        return $view->render([
            'app' => new CRestrictedAccessWidgetHelper($this->app),
            'page' => $this->page,
            'sidebar' => $this->sidebar->build()
        ]);
    }

    public function put()
    {
        $catId = $this->app->router->request()->getRequestData("id");
        $catId = explode('__', $catId);
        $categoryId = explode('_', $catId[0])[1];
        $marketplaceAccountId = explode('_', $catId[1])[1];

        $marketplaceAccount = \Monkey::app()->repoFactory->create('MarketplaceAccount')->findOneByStringId($marketplaceAccountId);;

        try {
            \Monkey::app()->repoFactory->beginTransaction();
            $this->app->dbAdapter->delete('ProductCategoryHasMarketplaceAccountCategory',
                ['marketplaceId' => $marketplaceAccount->marketplaceId,
                    'marketplaceAccountId' => $marketplaceAccount->id,
                    'productCategoryId' => $categoryId]);

            $value = $this->app->router->request()->getRequestData("value");
            if(!empty($value)) {
                $key = 'cmhtid' . $value;
                $marketplaceAccountCategoryIds = $this->app->cacheService->getCache('index')->get($key);
                if (!$marketplaceAccountCategoryIds) {
                    foreach ($marketplaceAccount->marketplaceAccountCategory as $marketplaceAccountCategory) {
                        /** @var CMarketplaceAccountCategory $marketplaceAccountCategory */
                        $tempKey = 'cmhtid' . $marketplaceAccountCategory->getHashKey('md5');
                        if($tempKey == $key) {
                            $this->app->cacheService->getCache('index')->set($tempKey, $marketplaceAccountCategory->printId());
                            $marketplaceAccountCategoryIds = $marketplaceAccountCategory->printId();
                            break;
                        }
                    }
                }
                $marketplaceAccountCategory = \Monkey::app()->repoFactory->create('MarketplaceAccountCategory')->findOneByStringId($marketplaceAccountCategoryIds);

                $this->app->dbAdapter->insert('ProductCategoryHasMarketplaceAccountCategory',
                    ['marketplaceId' => $marketplaceAccount->marketplaceId,
                        'marketplaceAccountId' => $marketplaceAccount->id,
                        'marketplaceAccountCategoryId' => $marketplaceAccountCategory->marketplaceCategoryId,
                        'productCategoryId' => $categoryId], false, true);
            }
            \Monkey::app()->repoFactory->commit();
        } catch (\Throwable $e) {
            \Monkey::app()->repoFactory->rollback();
            throw $e;
        }


    }

}