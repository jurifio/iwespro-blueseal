<?php
namespace bamboo\blueseal\jobs;

use bamboo\core\db\pandaorm\repositories\ARepo;
use bamboo\core\jobs\ACronJob;
use bamboo\domain\repositories\CProductRepo;

/**
 * Class CDispatchPreorderToFriend
 * @package bamboo\blueseal\jobs
 * @author Bambooshoot Team <emanuele@bambooshoot.agency>, ${DATE}
 * @copyright (c) Bambooshoot snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @since ${VERSION}
 */
class CGenerateDummyJob extends ACronJob
{
    /**
     * @param null $args
     */
    public function run($args = null)
    {
        $this->generateDummy();
        $this->report('CGenerateDummyJob','StartJob');
    }


    public function generateDummy()
    {
        try {
            /** @var  $productRepo CProductRepo */
            $productRepo = \Monkey::app()->repoFactory->create('Product');
             $query = "SELECT
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
               `pb`.slug as productBrandSlug,
               group_concat(`ds`.`size` ORDER BY `ds`.`size` ASC SEPARATOR '-') AS `problems`,
           if((p.id, p.productVariantId) IN (SELECT
                                                               ProductHasProductPhoto.productId,
                                                               ProductHasProductPhoto.productVariantId
                                                             FROM ProductHasProductPhoto), 'sì', 'no')                 AS hasPhotos,
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
                   if((p.id, p.productVariantId) IN (SELECT
                                                               ProductHasProductPhoto.productId,
                                                               ProductHasProductPhoto.productVariantId
                                                             FROM ProductHasProductPhoto), 'sì', 'no') ='sì' and
                    p.dummyPicture LIKE '%bs-dummy-16-9.png%' or p.dummyPicture like '%//assets//%' or p.dummyPicture = 'https://cdn.iwes.it/'

             GROUP BY `dp`.`productId`, `dp`.`productVariantId`, `dp`.`shopId`, phpc.productCategoryId";
          //  $query ='select id as productId, productVariantId from Product ';
            $res = $this->app->dbAdapter->query($query,[])->fetchAll();
            foreach ($res as $result) {
                $product = $productRepo->findOneBy(['id' => $result['productId'],'productVariantId' => $result['productVariantId']]);


                if (@getimagesize('https://cdn.iwes.it/'.$product->getPhoto(1, \bamboo\domain\entities\CProductPhoto::SIZE_THUMB))) {
                    $url='https://cdn.iwes.it/'.$product->getPhoto(1, \bamboo\domain\entities\CProductPhoto::SIZE_THUMB);
                }elseif(@getimagesize('https://cdn.iwes.it/'.$product->getPhoto(2, \bamboo\domain\entities\CProductPhoto::SIZE_THUMB))){
                    $url='https://cdn.iwes.it/'.$product->getPhoto(2, \bamboo\domain\entities\CProductPhoto::SIZE_THUMB);
                }elseif(@getimagesize('https://cdn.iwes.it/'.$product->getPhoto(1, \bamboo\domain\entities\CProductPhoto::SIZE_MEDIUM))){
                    $url='https://cdn.iwes.it/'.$product->getPhoto(2, \bamboo\domain\entities\CProductPhoto::SIZE_MEDIUM);
                }elseif(@getimagesize('https://cdn.iwes.it/'.$product->getPhoto(1, \bamboo\domain\entities\CProductPhoto::SIZE_BIG))){
                    $url='https://cdn.iwes.it/'.$product->getPhoto(1, \bamboo\domain\entities\CProductPhoto::SIZE_BIG);
                }elseif(@getimagesize('https://cdn.iwes.it/'.$product->getPhoto(1, \bamboo\domain\entities\CProductPhoto::SIZE_MEDIUM))){
                    $url='https://cdn.iwes.it/'.$product->getPhoto(1, \bamboo\domain\entities\CProductPhoto::SIZE_MEDIUM);
                }else{
                    $url='bs-dummy-16-9.png';
                }
                $product->dummyPicture = $url;
                $product->update();
            }
            $this->report('CGenerateDummyJob','Report','Dummy Generation Complete');
        }catch (\Throwable $e){
            $this->report('CGenerateDummyJob','error','Error on Dummy '.$e->getMessage());
        }
    }

}