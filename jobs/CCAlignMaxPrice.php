<?php
namespace bamboo\blueseal\jobs;

use bamboo\core\jobs\ACronJob;

/**
 * Class CAlignMaxPrice
 * @package bamboo\blueseal\jobs
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 06/12/2018
 * @since 1.0
 */
class CAlignMaxPrice extends ACronJob
{
    /**
     * @param null $args
     * @throws \bamboo\core\exceptions\BambooDBALException
     */
    public function run($args = null)
    {
        $this->alignPrice();
    }

    /**
     * @throws \bamboo\core\exceptions\BambooDBALException
     */
    public function alignPrice()
    {
        $products = \Monkey::app()->dbAdapter->query(
            'SELECT p.id, p.productVariantId
            FROM Product p
              JOIN ProductSku ps ON ps.productId = p.id AND ps.productVariantId = p.productVariantId
            WHERE ps.value in (ps.price) AND p.productStatusId = 6
            GROUP BY p.id, p.productVariantId',
            [])->fetchAll();

        /** @var \bamboo\domain\repositories\CProductRepo $pR */
        $pR = \Monkey::app()->repoFactory->create('Product');
        $c = 0;
        foreach ($products as $product){

            $maxPrice = \Monkey::app()->dbAdapter->query('
            SELECT max(ps.price) max
            FROM ProductSku ps
            WHERE ps.productId = ? AND ps.productVariantId = ?',
                [$product['id'], $product['productVariantId']])->fetch()['max'];

            $minPrice = \Monkey::app()->dbAdapter->query('
            SELECT min(ps.price) min
            FROM ProductSku ps
            WHERE ps.productId = ? AND ps.productVariantId = ?',
                [$product['id'], $product['productVariantId']])->fetch()['min'];

            if ($maxPrice == $minPrice) continue;

            try {

                \Monkey::app()->dbAdapter->beginTransaction();
                /** @var \bamboo\domain\entities\CProduct $p */
                $p = $pR->findOneBy(["id" => $product['id'], "productVariantId" => $product['productVariantId']]);

                /** @var \bamboo\domain\entities\CProductSku $psk */
                foreach ($p->productSku as $psk) {
                    $psk->price = $maxPrice;
                    $psk->update();

                    $shopHasProduct = $psk->shopHasProduct;
                    $shopHasProduct->price = $maxPrice;
                    $shopHasProduct->update();
                }

                /** @var \bamboo\domain\entities\CProductPublicSku $ppsk */
                foreach ($p->productPublicSku as $ppsk){
                    $ppsk->price = $maxPrice;
                    $ppsk->update();
                }

                \Monkey::app()->dbAdapter->commit();
                $this->report('AlignPrice', 'Aligned ' . $product['id'] . '-' . $product['productVariantId']);
                $c++;
            } catch(\Throwable $e){
                \Monkey::app()->dbAdapter->rollBack();
                $this->error('AlignPrice', 'Error on ' . $product['id'] . '-' . $product['productVariantId'], $e->getMessage());
            }
        }

        $this->report('AlignPrice', 'Updated ' . $c .' products');
    }
}