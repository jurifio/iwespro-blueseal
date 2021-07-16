<?php
namespace bamboo\blueseal\jobs;

use bamboo\core\jobs\ACronJob;

/**
 * Class CAlignExternalIdToProductJob
 * @package bamboo\blueseal\jobs
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 16/07/2021
 * @since 1.0
 */
class CAlignExternalIdToProductJob extends ACronJob
{
    /**
     * @param null $args
     * @throws \bamboo\core\exceptions\BambooDBALException
     */
    public function run($args = null)
    {
        $this->alignexternalId();
    }

    /**
     * @throws \bamboo\core\exceptions\BambooDBALException
     */
    public function alignexternalId()
    {
        $dirtyProducts = \Monkey::app()->dbAdapter->query(
            'SELECT dp.productId, dp.productVariantId,dp.extId
            FROM DirtyProduct dp
              JOIN Product p ON dp.productId = p.id AND dp.productVariantId = p.productVariantId
            WHERE  p.productStatusId in (6,11)
            GROUP BY p.id, p.productVariantId',
            [])->fetchAll();

        /** @var \bamboo\domain\repositories\CProductRepo $pR */
        $pR = \Monkey::app()->repoFactory->create('Product');
        $c = 0;
        foreach ($dirtyProducts as $dirtyProduct){
                $product=$pR->findOneBy(['id'=>$dirtyProduct['productId'],'productVariantId'=>$dirtyProduct['productVariantId']]);
                $product->externalId=$dirtyProduct['extId'];
                $product->update();
        }

        $this->report('AlignExternalIdToProductJob', 'Updated ' . $c .' products');
    }
}