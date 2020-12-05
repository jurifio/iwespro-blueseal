<?php

namespace bamboo\blueseal\jobs;

use bamboo\blueseal\marketplace\prestashop\CPrestashopProduct;
use bamboo\core\base\CObjectCollection;
use bamboo\core\jobs\ACronJob;
use bamboo\domain\entities\CPrestashopHasProduct;
use bamboo\domain\entities\CPrestashopHasProductHasMarketplaceHasShop;
use bamboo\domain\entities\CProductPublicSku;
use bamboo\domain\entities\CProductEan;
use bamboo\domain\entities\CProductSku;
use bamboo\domain\entities\CProduct;
use bamboo\domain\entities\CProductBrand;
use bamboo\domain\entities\CShop;
use PDO;
use PDOException;

/**
 * Class CAlignQuantityStockProductExternalShopJob
 * @package bamboo\blueseal\jobs
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 30/08/2019
 * @since 1.0
 *
 */


class CTagAllExclusiveManageJob extends ACronJob
{

    /**
     * @param null $args
     * @throws \bamboo\core\exceptions\BambooException
     * @throws \bamboo\core\exceptions\BambooORMInvalidEntityException
     * @throws \bamboo\core\exceptions\BambooORMReadOnlyException
     */
    public function run($args = null)
    {
        $this->tagExclusiveAll();
    }

    /**
     * @throws \bamboo\core\exceptions\BambooException
     * @throws \bamboo\core\exceptions\BambooORMInvalidEntityException
     * @throws \bamboo\core\exceptions\BambooORMReadOnlyException
     */
    private function tagExclusiveAll()
    {
        try {
            $res='';
            //prendo i dati passati in input
            $data = \Monkey::app()->router->request()->getRequestData();
            $tagExclusive = \Monkey::app()->repoFactory->create('TagExclusive')->findOneBy(['slug' => 'all']);
            $tagExclusiveId = $tagExclusive->id;
            $ProductHasTagExclusiveRepo = \Monkey::app()->repoFactory->create('ProductHasTagExclusive');
            $res = \Monkey::app()->dbAdapter->query('SELECT p.id, p.productVariantId from Product p where p.qty>0', []) -> fetchAll();

            foreach ($res as $result) {

                $phte = $ProductHasTagExclusiveRepo->findOneBy(['productId' => $result['id'],'productVariantId' => $result['productVariantId']]);
                if ($phte) {
                    continue;
                } else {
                    $phteInsert = $ProductHasTagExclusiveRepo->getEmptyEntity();
                    $phteInsert->productId = $result['id'];
                    $phteInsert->productVariantId = $result['productVariantId'];
                    $phteInsert->tagExclusiveId = $tagExclusiveId;
                    $phteInsert->insert();
                }
            }

        }catch(\Throwable $e){
            $this->report('CTagAllExclusiveManageJob','Error Insert Tag all error',$e->getLine().'-'.$e->getMessage());

        }
    }
}