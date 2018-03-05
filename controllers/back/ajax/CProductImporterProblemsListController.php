<?php

namespace bamboo\controllers\back\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\domain\entities\CProduct;
use bamboo\domain\entities\CShopHasProduct;

/**
 * Class CTestAjax.php
 * @package bamboo\app\controllers
 */
class CProductImporterProblemsListController extends AAjaxController
{
    public function get()
    {
        $bluesealBase = $this->app->baseUrl(false) . "/blueseal/";

        $query =
            "SELECT
              `p`.`id`                                                         AS `productId`,
              `p`.`productVariantId`                                           AS `productVariantId`,
              concat(`p`.`id`, '-', `p`.`productVariantId`)                    AS `productCode`,
              concat(`p`.`itemno`, ' # ', `pv`.`name`)                         AS `code`,
              `s`.`name`                                                       AS `shop`,
              `s`.`id`                                                         AS `shopId`,
              `pb`.`name`                                                      AS `brand`,
              `p`.`externalId`                                                 AS `externalId`,
              `ps`.`name`                                                      AS `status`,
              concat_ws('-',`psg`.`locale`, `psmg`.`name`)                     AS `sizeGroup`,
              `p`.`creationDate`                                               AS `creationDate`,
              group_concat(`ds`.`size` ORDER BY `ds`.`size` ASC SEPARATOR '-') AS `problems`,
              productCategoryId AS categoryId
            FROM `Product` `p`
              JOIN `ProductVariant` `pv` ON `pv`.`id` = `p`.`productVariantId`
              JOIN `ProductBrand` `pb` ON `p`.`productBrandId` = `pb`.`id`
              JOIN `ProductStatus` `ps` ON `p`.`productStatusId` = `ps`.`id`
              JOIN `DirtyProduct` `dp` ON (`p`.`id` = `dp`.`productId`) AND (`p`.`productVariantId` = `dp`.`productVariantId`)
              JOIN `DirtySku` `ds` ON `dp`.`id` = `ds`.`dirtyProductId`
              JOIN `ShopHasProduct` `sp` ON (`dp`.`productId` = `sp`.`productId`)
                                              AND (`dp`.`productVariantId` = `sp`.`productVariantId`)
                                              AND (`dp`.`shopId` = `sp`.`shopId`)
              JOIN `ProductSizeGroup` `psg` ON `sp`.`productSizeGroupId` = `psg`.`id`
              JOIN `Shop` `s` ON `sp`.`shopId` = `s`.`id`
              LEFT JOIN ProductSizeMacroGroup psmg ON psg.productSizeMacroGroupId = psmg.id

              LEFT JOIN ProductHasProductCategory phpc ON p.id = phpc.productId AND p.productVariantId = phpc.productVariantId
            WHERE
              `ps`.`id` NOT IN (6, 7, 8, 12, 13)
               AND (`s`.`importer` IS NOT NULL)
               AND ((`ds`.`status` not in ('ok', 'exclude') ) OR ds.status IS NULL )
            GROUP BY `dp`.`productId`, `dp`.`productVariantId`, `dp`.`shopId`, phpc.productCategoryId
            HAVING (sum(`ds`.`qty`) > 0)";

        $datatable = new CDataTables($query, ['productId', 'productVariantId', 'shopId'], $_GET, true);
        if (!empty($this->authorizedShops)) {
            $datatable->addCondition('shopId', $this->authorizedShops);
        }
        $datatable->doAllTheThings(true);

        $modifica = $bluesealBase . "prodotti/modifica";
        $shopHasProductRepo = \Monkey::app()->repoFactory->create('ShopHasProduct');
        foreach ($datatable->getResponseSetData() as $key => $row) {
            /** @var CShopHasProduct $shopHasProduct */
            $shopHasProduct = $shopHasProductRepo->findOne($row);
            $cats = [];
            foreach ($shopHasProduct->product->productCategoryTranslation as $cat) {
                $path = $this->app->categoryManager->categories()->getPath($cat->productCategoryId);
                unset($path[0]);
                $cats[] = '<span>' . implode('/', array_column($path, 'slug')) . '</span>';
            }

            /** @var CProduct $val */

            $creationDate = new \DateTime($shopHasProduct->product->creationDate);

            $row["DT_RowId"] = $shopHasProduct->printId();
            $row["DT_RowClass"] = 'colore';
            $row["productCode"] = $this->app->getUser()->hasPermission('/admin/product/edit') ? '<span class="tools-spaced"><a href="' . $modifica . '?id=' . $shopHasProduct->productId . '&productVariantId=' . $shopHasProduct->productVariantId . '">' . $shopHasProduct->printId() . '</a></span>' : $shopHasProduct->product->printId();
            $row["shop"] = $shopHasProduct->shop->name;
            $row["nshop"] = $shopHasProduct->product->shopHasProduct->count();
            $row["code"] = $shopHasProduct->product->printCpf();
            $macroname = explode("_", explode("-", $shopHasProduct->productSizeGroup->productSizeMacroGroup->name)[0])[0];
            $row["sizeGroup"] = '<span class="small">' . $shopHasProduct->productSizeGroup->locale . '-' . $macroname . '</span>';
            $row["dummyPicture"] = '<img width="80" src="' . $shopHasProduct->product->getDummyPictureUrl() . '">';
            $row["brand"] = $shopHasProduct->product->productBrand->name;
            $row["categoryId"] = $shopHasProduct->product->getLocalizedProductCategories('<br>', '/');
            $row["status"] = $shopHasProduct->product->productStatus->name;
            $row["creationDate"] = $creationDate->format('d-m-Y H:i');
            $row["problems"] = $this->parseProblem($shopHasProduct);
            $datatable->setResponseDataSetRow($key, $row);
        }
        return $datatable->responseOut();
    }

    /**
     * @param CShopHasProduct $shopHasProduct
     * @return string
     */
    private function parseProblem(CShopHasProduct $shopHasProduct)
    {
        $message = "[500] Size Mismatch";
        $sizes = $this->app->dbAdapter->query(
            'SELECT size 
                    FROM DirtyProduct dp 
                      JOIN DirtySku ds ON dp.id = ds.dirtyProductId 
                    WHERE  
                    dp.productId = :productId AND 
                    dp.productVariantId = :productVariantId AND 
                    dp.shopId = :shopId', $shopHasProduct->getIds())->fetchAll();
        $newSize = [];
        foreach ($sizes as $size) {
            $newSize[] = $size['size'];
        }
        $message .= " " . implode('-', $newSize);
        return '<span>' . $message . '</span>';
    }

    public function post()
    {
        throw new \Exception();
    }

    public function delete()
    {
        throw new \Exception();
    }
}