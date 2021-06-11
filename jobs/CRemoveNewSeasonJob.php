<?php

namespace bamboo\blueseal\jobs;

use bamboo\core\base\CObjectCollection;
use bamboo\core\jobs\ACronJob;
use bamboo\domain\entities\CProduct;
use bamboo\domain\entities\CProductHasTag;
use bamboo\domain\entities\CProductBrand;
use bamboo\domain\entities\CShop;
use bamboo\domain\entities\Tag;
use PDO;
use PDOException;
use DateTime;

/**
 * Class CRemoveNewSeasonJob
 * @package bamboo\blueseal\jobs
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 11/06/2021
 * @since 1.0
 */
class CRemoveNewSeasonJob extends ACronJob
{

    /**
     * @param null $args
     * @throws \PrestaShopWebserviceException
     * @throws \bamboo\core\exceptions\BambooException
     * @throws \bamboo\core\exceptions\BambooORMInvalidEntityException
     * @throws \bamboo\core\exceptions\BambooORMReadOnlyException
     */
    public function run($args = null)
    {
        $this->removeTagNewSeason();
    }

    /**
     * @throws \PrestaShopWebserviceException
     * @throws \bamboo\core\exceptions\BambooException
     * @throws \bamboo\core\exceptions\BambooORMInvalidEntityException
     * @throws \bamboo\core\exceptions\BambooORMReadOnlyException
     */
    private function removeTagNewSeason()
    {

        $productRepo = \Monkey::app()->repoFactory->create('Product');
        $productHasTagRepo = \Monkey::app()->repoFactory->create('ProductHasTag');
        $this->report('Start Insert New Season  ','Shop To Update Cartechini');
        $sqlTag = 'select id from Tag where slug ="spc-new-season"';
        $res = \Monkey::app()->dbAdapter->query($sqlTag,[]);
        $tagId=93;
        foreach ($res as $result) {
            $tagId = $result['id'];
        }

        try {
            $product = $productRepo->findAll();
            if (!empty($product)) {
                foreach ($product as $row) {
                    $productId = $row->id;
                    $productVariantId = $row->productVariantId;

                        $productHasTagFind = $productHasTagRepo->findOneBy(['productId' => $productId,'productVariantId' => $productVariantId,'tagId' => $tagId]);
                        if ($productHasTagFind != null) {
                            $productHasTagFind->delete();
                            //  $this->report('Insert Tag All  ', 'insert in ProductHasTag '.$productId.'-'.$productVariantId);
                        }


                }
            }
        } catch (PDOException $e) {
            $res .= $e->getMessage();
            $this->report('Remove Tag New Season',$res);
        }


    }
}