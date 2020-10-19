<?php

namespace bamboo\domain\repositories;

use bamboo\core\base\CObjectCollection;
use bamboo\core\db\pandaorm\repositories\ARepo;
use bamboo\core\exceptions\BambooException;
use bamboo\domain\entities\CContractDetails;
use bamboo\domain\entities\CFoison;
use bamboo\domain\entities\CProductBatch;
use bamboo\domain\entities\CProductBatchDetails;
use bamboo\domain\entities\CProductBatchHasProductName;
use bamboo\domain\entities\CProductBatchTextManage;
use bamboo\domain\entities\CProductBatchTextManagePhoto;
use bamboo\domain\entities\CUser;
use bamboo\domain\entities\CWorkCategory;
use bamboo\domain\entities\CWorkCategorySteps;
use bamboo\utils\time\SDateToolbox;
use bamboo\utils\time\STimeToolbox;

/**
 * Class CProductBatchRepo
 * @package bamboo\domain\repositories
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 16/03/2018
 * @since 1.0
 */
class CProductBatchRepo extends ARepo
{
    /**
     * @param $scheduledDelivery
     * @param $value
     * @param $contractDetailsId
     * @param $products
     * @return \bamboo\core\db\pandaorm\entities\AEntity|CProductBatch
     */
    public function createNewProductBatch($scheduledDelivery, $value, $contractDetailsId, $products)
    {

        try {
            /** @var CContractDetails $contractDetails */
            $contractDetails = \Monkey::app()->repoFactory->create('ContractDetails')->findOneBY(['id' => $contractDetailsId]);

            $sectionalCodeId = $contractDetails->workCategory->sectionalCodeId;

            /** @var CSectionalRepo $sectionalRepo */
            $sectionalRepo = \Monkey::app()->repoFactory->create('Sectional');

            /** @var CProductBatch $productBatch */
            $productBatch = $this->getEmptyEntity();
            $productBatch->scheduledDelivery = $scheduledDelivery;
            $productBatch->value = $value;
            $productBatch->contractDetailsId = $contractDetailsId;
            $productBatch->sectional = $sectionalRepo->createNewSectionalCode($sectionalCodeId);
            $productBatch->isUnassigned = 0;
            $productBatch->smartInsert();

            /** @var CProductBatchDetailsRepo $productBatchDetailsRepo */
            $productBatchDetailsRepo = \Monkey::app()->repoFactory->create('ProductBatchDetails');
            $productBatchDetailsRepo->createNewProductBatchDetails($productBatch, $products);
        } catch (\Throwable $e) {
        }

        return $productBatch;
    }


    /**
     * @param $id
     * @return bool
     * @throws BambooException
     * @throws \bamboo\core\exceptions\BambooORMInvalidEntityException
     * @throws \bamboo\core\exceptions\BambooORMReadOnlyException
     */
    public function closeProductBatch($id)
    {

        /** @var CProductBatch $productBatch */
        $productBatch = $this->findOneBy(['id' => $id]);

        if ($productBatch->closingDate == 0) {
            $productBatch->closingDate = date('Y-m-d H:i:s');
            $productBatch->update();

            $foison = $productBatch->contractDetails->contracts->foison;
            $foison->activeProductBatch = null;
            $foison->update();

            if (is_null($productBatch->unfitDate)) $this->qualityRank($productBatch);
            /** @var CFoison $foison */
            $foison = $productBatch->contractDetails->contracts->foison;
            $foison->totalRank(true);

            /** @var CFoisonRepo $fR */
            $fR = \Monkey::app()->repoFactory->create('Foison');
            $fR->checkStatusForEachWorkCategory($foison->id);
        }

        return true;
    }

    public function calculateProductBatchCost($productBatchId)
    {
        /** @var CProductBatch $pB */
        $pB = $this->findOneBy(['id' => $productBatchId]);


        $numberOfProducts = count($pB->getElements());

        $type = $pB->contractDetails->isVariable;

        if ($type == 0) {
            $newPrice = $pB->contractDetails->workPriceList->price * $numberOfProducts;
        } elseif ($type == 1) {
            $newPrice = $pB->unitPrice * $numberOfProducts;
        }

        return $newPrice;

    }

    /**
     * @param $id
     * @return bool
     * @throws BambooException
     * @throws \bamboo\core\exceptions\BambooORMInvalidEntityException
     * @throws \bamboo\core\exceptions\BambooORMReadOnlyException
     */
    public function acceptProductBatch($id)
    {

        $pB = $this->findOneBy(['id' => $id]);

        if ($pB->confirmationDate != 0) {
            return false;
        }
        $date = new \DateTime();
        $pB->confirmationDate = date_format($date, 'Y-m-d H:i:s');
        $pB->update();

        return $pB->id;
    }

    /**
     * @param $productBatch
     * @param $value
     * @param $contractDetailsId
     * @return CProductBatch
     * @throws BambooException
     * @throws \Exception
     * @throws \bamboo\core\exceptions\BambooORMInvalidEntityException
     * @throws \bamboo\core\exceptions\BambooORMReadOnlyException
     */
    public function associateProductBatch($productBatch, $contractDetailsId)
    {
        $schedDelivery = SDateToolbox::GetDateAfterAddedDays(null, $productBatch->estimatedWorkDays)->format('Y-m-d 23:59:59');


        /** @var CContractDetails $contractDetails */
        $contractDetails = \Monkey::app()->repoFactory->create('ContractDetails')->findOneBy(['id' => $contractDetailsId]);

        $sectionalCodeId = $contractDetails->workCategory->sectionalCodeId;

        /** @var CSectionalRepo $sectionalRepo */
        $sectionalRepo = \Monkey::app()->repoFactory->create('Sectional');
        $date = new \DateTime();
        /** @var CProductBatch $productBatch */
        $productBatch->scheduledDelivery = $schedDelivery;
        $productBatch->contractDetailsId = $contractDetailsId;
        $productBatch->sectional = $sectionalRepo->createNewSectionalCode($sectionalCodeId);
        $productBatch->marketplace = 0;
        $productBatch->isUnassigned = 0;
        $productBatch->confirmationDate = date_format($date, 'Y-m-d H:i:s');
        $productBatch->tolleranceDelivery = SDateToolbox::GetDateAfterAddedDays(STimeToolbox::GetDateTime($schedDelivery), 5)->format('Y-m-d 23:59:59');
        $productBatch->update();

        /** @var CWorkCategoryStepsRepo $catStR */
        $catStR = \Monkey::app()->repoFactory->create('WorkCategorySteps');

        $catId = $productBatch->contractDetails->workCategory->id;

        /** @var CWorkCategorySteps $initStep */
        $initStep = $catStR->getFirstStepsFromCategoryId($catId);

        /** @var CObjectCollection $elems */
        $elems = $productBatch->getElements();

        foreach ($elems as $elem) {
            $elem->workCategoryStepsId = $initStep->id;
            $elem->update();
        }

        $items = count($elems);
        $type = $contractDetails->isVariable;

        if ($type == 0) {
            $newPrice = $productBatch->contractDetails->workPriceList->price * $items;
        } elseif ($type == 1) {
            $newPrice = $productBatch->unitPrice * $items;
        }
        $productBatch->value = $newPrice;
        $productBatch->update();

        return $productBatch;
    }


    public function checkRightLanguage($pbId, $langId)
    {

        /** @var CProductBatch $pb */
        $pb = $this->findOneBy(['id' => $pbId]);

        if (is_null($pb)) return false;


        if (is_null($pb->contractDetailsId)) {
            $wk = $pb->workCategoryId;
        } else {
            $wk = $pb->contractDetails->workCategory->id;
        }

        $correct = false;
        switch ($langId) {
            case 2:
                if ($wk == CWorkCategory::NAME_ENG) $correct = true;
                break;
            case 3:
                if ($wk == CWorkCategory::NAME_DTC) $correct = true;
                break;
            case 5:
                if ($wk == CWorkCategory::NAME_RUS) $correct = true;
                break;
            case 6:
                if ($wk == CWorkCategory::NAME_CHI) $correct = true;
                break;
            case 7:
                if ($wk == CWorkCategory::NAME_FRE) $correct = true;
                break;
        }

        return $correct;
    }

    /**
     * @param CProductBatch $productBatch
     * @return int|mixed
     * @throws BambooException
     * @throws \Exception
     * @throws \bamboo\core\exceptions\BambooORMInvalidEntityException
     * @throws \bamboo\core\exceptions\BambooORMReadOnlyException
     */
    public function timingRank(CProductBatch $productBatch): int
    {

        $tolleranceClosing = SDateToolbox::GetDateAfterAddedDays(STimeToolbox::GetDateTime($productBatch->scheduledDelivery), 5)->format('Y-m-d 23:59:59');
        if ($productBatch->requestClosingDate <= $productBatch->scheduledDelivery) {
            $productBatch->timingRank = 10;
        } else if ($productBatch->requestClosingDate <= $tolleranceClosing && $productBatch->requestClosingDate > $productBatch->scheduledDelivery) {
            $productBatch->timingRank = 2;
        }

        $productBatch->update();
        return $productBatch->timingRank;
    }

    public function qualityRank(CProductBatch $productBatch)
    {
        $nPb = count($productBatch->getElements());
        $nNpb = count($productBatch->getNormalizedElements());
        $qRank = round($nNpb / $nPb * 10, 2);
        $productBatch->qualityRank = $qRank;

        $productBatch->update();

        return $productBatch->qualityRank;
    }


    /**
     * @param CProductBatch $productBatch
     * @return bool
     * @throws BambooException
     * @throws \bamboo\core\exceptions\BambooORMInvalidEntityException
     * @throws \bamboo\core\exceptions\BambooORMReadOnlyException
     */
    public function duplicateProductBatchFromCancelled(CProductBatch $productBatch)
    {

        $notNormalized = $productBatch->getNotNormalizedElements();
        /** @var CSectionalRepo $sRepo */
        $sRepo = \Monkey::app()->repoFactory->create('Sectional');

        /** @var CProductBatch $newPB */
        $newPB = $this->getEmptyEntity();
        $newPB->name = $productBatch->name;
        $newPB->description = $productBatch->description;
        //$newPB->sectional = $sRepo->createNewSectionalCode($productBatch->workCategory->sectionalCodeId);
        $newPB->value = count($notNormalized) * $productBatch->unitPrice;
        $newPB->workCategoryId = $productBatch->workCategoryId;
        $newPB->marketplace = 1;
        $newPB->estimatedWorkDays = $this->recalculateEstimatedWorkDay($productBatch);
        $newPB->unitPrice = $productBatch->unitPrice;
        $newPB->smartInsert();

        switch ($newPB->workCategoryId) {
            case CWorkCategory::NORM:
                /** @var CProductBatchDetailsRepo $pbdR */
                $pbdR = \Monkey::app()->repoFactory->create('ProductBatchDetails');
                $products = [];
                foreach ($notNormalized as $n) {
                    $products[] = $n->productId . '-' . $n->productVariantId;
                }

                $pbdR->insertProductInEmptyProductBatch($newPB->id, $products);
                break;
            case CWorkCategory::BRAND:
                /** @var CProductBatchHasProductBrandRepo $pbR */
                $pbR = \Monkey::app()->repoFactory->create('ProductBatchHasProductBrand');
                $brandIds = [];
                foreach ($notNormalized as $n) {
                    $brandIds[] = $n->productBrandId;
                }

                $pbR->insertNewProductBrand($newPB->id, $brandIds);
                break;
            case CWorkCategory::NAME_ENG:
            case CWorkCategory::NAME_DTC:
            case CWorkCategory::NAME_RUS:
            case CWorkCategory::NAME_CHI:
            case CWorkCategory::NAME_FRE:
                /** @var CProductBatchHasProductNameRepo $pnR */
                $pnR = \Monkey::app()->repoFactory->create('ProductBatchHasProductName');
                $langId = $notNormalized[0]->langId;
                $productNames = [];
                foreach ($notNormalized as $n) {
                    $productNames[] = $n->productName;
                }

                $pnR->insertNewProductNameFromCopy($newPB, $productNames, $langId);
                break;
            case CWorkCategory::TXT_FAS:
            case CWorkCategory::TXT_FAS_BLOG:
            case CWorkCategory::TXT_INFL:
            case CWorkCategory::TXT_PRT:
            case CWorkCategory::TXT_BRAND:
            case CWorkCategory::TXT_FB:
            case CWorkCategory::TXT_COPY_BLOG_POST:
            case CWorkCategory::TXT_COPY_BRAND:
            case CWorkCategory::TXT_FB_CR:
            case CWorkCategory::TXT_FB_VID:
            case CWorkCategory::TXT_IN_PHOTO_FEED:
            case CWorkCategory::TXT_IN_PHOTO_STORY:
            case CWorkCategory::TXT_IN_VIDEO_FEED:
            case CWorkCategory::TXT_IN_VIDEO_STORY:
            case CWorkCategory::POST_YOUTUBE_VIDEO:
            case CWorkCategory::POST_TWITTER_VIDEO:
            case CWorkCategory::POST_TIKTOK_VIDEO:
            case CWorkCategory::POST_WHATSAPP:
            case CWorkCategory::STREAM_TWITCH:
            case CWorkCategory::STREAM_YOUTUBE:

                /** @var CProductBatchTextManageRepo $pbtmR */
                $pbtmR = \Monkey::app()->repoFactory->create('ProductBatchTextManage');


                /** @var CProductBatchTextManage $n */
                foreach ($notNormalized as $n) {
                    /** @var CProductBatchTextManage $newProductBatchTextManage */
                    $newProductBatchTextManage = $pbtmR->insertNewProductBatchTextManage($newPB, $n->theme, $n->description);

                    $oldPhotos = $n->productBatchTextManagePhoto->isEmpty() ? null : $n->productBatchTextManagePhoto->findByKey('isDummy', 1);

                    /** @var CProductBatchTextManagePhotoRepo $pbtmpR */
                    $pbtmpR = \Monkey::app()->repoFactory->create('ProductBatchTextManagePhoto');

                    if (!is_null($oldPhotos)) {
                        /** @var CProductBatchTextManagePhoto $photo */
                        foreach ($oldPhotos as $photo) {
                            $pbtmpR->insertNewProductBatchTextManagePhoto($newProductBatchTextManage->id, $photo->imageName, 1);
                        }
                    }
                }

                break;
        }

        return true;

    }

    private function recalculateEstimatedWorkDay(CProductBatch $pb)
    {
        $oldNumberElems = count($pb->getElements());
        $newNumberElems = count($pb->getNormalizedElements());
        $oldDay = $pb->estimatedWorkDays;

        return ceil($oldDay * $newNumberElems / $oldNumberElems);
    }

    public function createEmptyProductBatch($unitPrice, $name, $desc, $deliveryTime, $workCat, $mp) : CProductBatch
    {

        /** @var CProductBatch $pb */
        $pb = $this->getEmptyEntity();
        $pb->paid = 0;
        $pb->description = $desc;
        $pb->estimatedWorkDays = $deliveryTime;
        $pb->name = $name;
        $pb->workCategoryId = $workCat;
        $pb->unitPrice = $unitPrice;
        $pb->isUnassigned = 0;
        if($mp != "false") $pb->marketplace = 1;

        $pb->smartInsert();

        return $pb;
    }
}