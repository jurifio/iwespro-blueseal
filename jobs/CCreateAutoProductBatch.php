<?php

namespace bamboo\blueseal\jobs;


use bamboo\core\db\pandaorm\repositories\CRepo;
use bamboo\core\jobs\ACronJob;
use bamboo\domain\entities\CProductBatch;
use bamboo\domain\entities\CProductCategoryTranslation;
use bamboo\domain\entities\CProductDescriptionTranslation;
use bamboo\domain\repositories\CEmailRepo;
use bamboo\domain\repositories\CProductBatchRepo;
use bamboo\domain\repositories\CProductRepo;


/**
 * Class CCreateAutoProductBatch
 * @package bamboo\blueseal\jobs
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 21/12/2018
 * @since 1.0
 */
class CCreateAutoProductBatch extends ACronJob
{
    /**
     * @param null $args
     * @throws \bamboo\core\exceptions\BambooDBALException
     * @throws \bamboo\core\exceptions\BambooException
     * @throws \bamboo\core\exceptions\BambooORMInvalidEntityException
     * @throws \bamboo\core\exceptions\BambooORMReadOnlyException
     */
    public function run($args = null)
    {
        $this->report('Create Product Batch', "Starting Creating");
        $this->createPB();
        $this->report('Create Product Batch', "End Creating");
    }

    /**
     * @throws \bamboo\core\exceptions\BambooDBALException
     * @throws \bamboo\core\exceptions\BambooException
     * @throws \bamboo\core\exceptions\BambooORMInvalidEntityException
     * @throws \bamboo\core\exceptions\BambooORMReadOnlyException
     */
    public function createPB()
    {
        try {
            \Monkey::app()->dbAdapter->beginTransaction();
            //Query
            $sqlProductWithNoDetails =
                "
            SELECT p.id productId, p.productVariantId productVariantId, psaNotNull.productId sheetId, psaNotNull.productVariantId sheetVariantId
                FROM Product p
                  LEFT JOIN (
                              SELECT
                                psa.productId,
                                psa.productVariantId,
                                COUNT(*) AS num
                              FROM Product p1
                                JOIN ProductSheetActual psa ON p1.id = psa.productId AND p1.productVariantId = psa.productVariantId
                              GROUP BY psa.productId, psa.productVariantId
                              HAVING num < 3
                            ) psaNotNull ON psaNotNull.productId = p.id AND psaNotNull.productVariantId = p.productVariantId
                  LEFT JOIN ProductBatchDetails pbd ON p.id = pbd.productId AND p.productVariantId = pbd.productVariantId
                WHERE p.productStatusId = 6 AND pbd.productId IS NULL;
             ";

            $psNoDetails = \Monkey::app()->dbAdapter->query($sqlProductWithNoDetails, [])->fetchAll();

            /** @var CProductRepo $prRepo */
            $prRepo = \Monkey::app()->repoFactory->create('Product');

            /** @var CRepo $pDTR */
            $pDTR = \Monkey::app()->repoFactory->create('ProductDescriptionTranslation');

            $productsArr = [];

//Inserisco i prodotti
            foreach ($psNoDetails as $psNoDetail) {

                //Se non ha la categoria salto
                $pHpC = \Monkey::app()->dbAdapter->query('
                                                          SELECT min(phpc.productCategoryId) AS minCat
                                                          FROM ProductHasProductCategory phpc
                                                          JOIN Product p ON p.id = phpc.productId AND p.productVariantId = phpc.productVariantId
                                                          WHERE p.id = ? AND p.productVariantId = ?
                                                          ', [$psNoDetail['productId'], $psNoDetail['productVariantId']])->fetch();


                if (is_null($pHpC['minCat'])) continue;

                if (is_null($psNoDetail['sheetId']) && is_null($psNoDetail['sheetVariantId'])) {

                    //Se non ha i dettagli e non ha la descrizione salto
                    /** @var CProductDescriptionTranslation $pDT */
                    $pDT = $pDTR->findOneBy(['productId' => $psNoDetail['productId'], 'productVariantId' => $psNoDetail['productVariantId']]);

                    if (is_null($pDT)) continue;
                }

                $productsArr[$pHpC['minCat']][] = $psNoDetail['productId'] . '-' . $psNoDetail['productVariantId'];
            }
            if (!empty($productsArr)) {

                $batchFirst = [];
                $c = 0;
                $i = 0;
                foreach ($productsArr as $k => $groupProduct) {
                    foreach ($groupProduct as $product) {
                        $c++;
                        $batchFirst[$i][$k][] = $product;
                        if ($c == 100) {
                            $c = 0;
                            $i++;
                        }
                    }
                }


                /** @var CProductBatchRepo $pbRepo */
                $pbRepo = \Monkey::app()->repoFactory->create('ProductBatch');
                /** @var \bamboo\domain\repositories\CProductBatchDetailsRepo $pbDRepo */
                $pbDRepo = \Monkey::app()->repoFactory->create('ProductBatchDetails');
                /** @var CRepo $pCR */
                $pCR = \Monkey::app()->repoFactory->create('ProductCategoryTranslation');
                foreach ($batchFirst as $singleBatchFirst) {

                    $keys = array_keys($singleBatchFirst);

                    $descr = 'Normalizzazione prodotti. Categorie interessate: ';
                    foreach ($keys as $key) {
                        /** @var CProductCategoryTranslation $pC */
                        $pC = $pCR->findOneBy(['productCategoryId' => $key, 'langId' => 1]);
                        $catName = is_null($pC) ? 'Categoria non definita' : $pC->name;
                        $descr .= $catName . ', ';
                    }

                    /** @var CProductBatch $pb */
                    $pb = $pbRepo->createEmptyProductBatch(0.2, 'Normalizzazione prodotti', $descr, 1, 1, 1);

                    foreach ($singleBatchFirst as $listProduct) {
                        $pbDRepo->insertProductInEmptyProductBatch($pb->id, $listProduct);
                    }
                }
            }
            \Monkey::app()->dbAdapter->commit();
        } catch (\Throwable $e){
            \Monkey::app()->dbAdapter->rollBack();
            $this->error('Error on creating batch', $e->getMessage());

            /** @var CEmailRepo $mailRp */
            $mailRp = \Monkey::app()->repoFactory->create('Email');
            $mailRp->newMail('it@iwes.it', ['it@iwes.it'], [], [], 'Error while creating product batch', $e->getMessage());

            return false;
        }

        return true;
    }
}