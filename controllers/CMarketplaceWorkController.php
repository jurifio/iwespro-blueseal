<?php
namespace bamboo\blueseal\controllers;

use bamboo\core\base\CObjectCollection;
use bamboo\domain\entities\CUser;
use bamboo\ecommerce\views\VBase;
use bamboo\core\theming\CRestrictedAccessWidgetHelper;

/**
 * Class CMarketplaceWorkController
 * @package bamboo\blueseal\controllers
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 21/08/2018
 * @since 1.0
 */
class CMarketplaceWorkController extends ARestrictedAccessRootController
{
    protected $fallBack = "blueseal";
    protected $pageSlug = "marketplace_work";

    /**
     * @return string
     * @throws \bamboo\core\exceptions\BambooDBALException
     * @throws \bamboo\core\exceptions\RedPandaInvalidArgumentException
     */
    public function get()
    {
        $view = new VBase(array());
        $view->setTemplatePath($this->app->rootPath().$this->app->cfg()->fetch('paths','blueseal').'/template/marketplace_work.php');

        $interestIds = [];
        $unallowedProductBatch = null;
        /** @var CUser $user */
        $user = \Monkey::app()->getUser();
        $permission = null;
        if($user->hasPermission('allShops')){
            $permission = true;
        } else if($user->hasPermission('worker')){
            $permission = false;
        }
        if($permission){
            $wcs = \Monkey::app()->dbAdapter->query('SELECT id from WorkCategory', [])->fetchAll();
            foreach ($wcs as $wc) {
                $interestIds[] = (int)$wc["id"];
            }
        } else {
            $interestIds = $user->foison->getInterestId();
            $noInterstIds = $user->foison->nonInterestId();
            $rowUIds = implode(',', $noInterstIds);
            $uQuery = "SELECT *
        FROM ProductBatch pb
        WHERE pb.marketplace = 1 and pb.workCategoryId in ($rowUIds)";
            $unallowedProductBatch = \Monkey::app()->repoFactory->create('ProductBatch')->findBySql($uQuery);
       }

        $rowIds = implode(',', $interestIds);
        $query = "SELECT *
        FROM ProductBatch pb
        WHERE pb.marketplace = 1 and pb.workCategoryId in ($rowIds)";
        /** @var CObjectCollection $productsBatch */
        $productsBatch = \Monkey::app()->repoFactory->create('ProductBatch')->findBySql($query);

        return $view->render([
            'user'=>$user,
            'permission' => $permission,
            'productBatch' => $productsBatch,
            'unallowedProductBatch'=>$unallowedProductBatch,
            'app' => new CRestrictedAccessWidgetHelper($this->app),
            'page' => $this->page,
            'sidebar' => $this->sidebar->build()
        ]);
    }
}