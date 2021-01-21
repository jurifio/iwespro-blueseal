<?php

namespace bamboo\blueseal\controllers;

use bamboo\core\theming\CRestrictedAccessWidgetHelper;
use bamboo\ecommerce\views\VBase;
use bamboo\core\router\ANodeController;
use bamboo\helpers\CWidgetCatalogHelper;

/**
 * Class CEbayMarketplaceProductListController
 * @package bamboo\blueseal\controllers
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 14/03/2019
 * @since 1.0
 */
class CAmazonMarketplaceDisplayCatalogProductListController extends ARestrictedAccessRootController
{
    protected $fallBack = "blueseal";
    protected $pageSlug = "amazon_marketplacedisplaycatalog_product";

    public function get()
    {
        $marketplaceAccount=\Monkey::app()->repoFactory->create('MarketplaceHasShop')->findBy(['marketplaceId'=>4,'isActive'=>1]);
        $view = new VBase(array());
        if(isset($_GET['marketplacehasshopid'])){
            $marketplaceHasShopId=$_GET['marketplacehasshopid'];
        } else{
            $marketplaceHasShopId='0';
        }
        if($marketplaceHasShopId!='0'){
            $marketplaceHasShopIdFind=' and phphmhs.marketplaceHasShopId='.$_GET['marketplacehasshopid'];
        } else{
            $marketplaceHasShopIdFind=' ';
        }
        if(isset($_GET['productbrandid'])){
            $productBrandId=$_GET['productbrandid'];
        } else{
            $productBrandId='0';
        }
        if( $productBrandId!=0){
            $productBrandIdFind=' and p.productBrandId='.$_GET['productbrandid'];
        } else{
            $productBrandIdFind=' ';
        }
        if(isset($_GET['productcategoryid'])){
            $productCategoryId=$_GET['productcategoryid'];
        } else{
            $productCategoryId='0';
        }
        if($productCategoryId!=0){
            $productCategoryIdFind=' and phpc.productCategoryId='.$_GET['productcategoryid'];
        } else{
            $productCategoryIdFind= ' ';
        }
        if(isset($_GET['pageid'])){

            $pageId=$_GET['pageid'];
            $offset=$_GET['pageid']*100;
            $sqlpagination="SELECT ceil(COUNT(*)/100) as countItem 
            from PrestashopHasProductHasMarketplaceHasShop phphmhs
            join Product p on phphmhs.productId=p.id and phphmhs.productVariantId=p.productVariantId
            join ProductBrand pb on p.productBrandId=pb.id
            join ProductHasProductCategory phpc on p.id=phpc.productId and p.productVariantId = phpc.productVariantId
           join MarketplaceHasShop mphs on phphmhs.marketplaceHasshopId=mphs.id 
            join Marketplace m on    m.id=mphs.marketplaceId 
            where 1=1 and   p.qty>0 and m.id=4 and phphmhs.isPublished=1  ".$marketplaceHasShopIdFind.$productBrandIdFind.$productCategoryIdFind;
            $countPages=\Monkey::app()->dbAdapter->query($sqlpagination,[])->fetchAll();
            foreach($countPages as $countPage) {
                $pageCount=$countPage['countItem'];
            }

        } else{
            $pageId=1;
            $offset=0;
            $sqlpagination="SELECT ceil(COUNT(*)/100) as countItem 
            from PrestashopHasProductHasMarketplaceHasShop phphmhs
            join Product p on phphmhs.productId=p.id and phphmhs.productVariantId=p.productVariantId
            join ProductBrand pb on p.productBrandId=pb.id
            join ProductHasProductCategory phpc on p.id=phpc.productId and p.productVariantId = phpc.productVariantId
            join MarketplaceHasShop mphs on phphmhs.marketplaceHasshopId=mphs.id 
            join Marketplace m on    m.id = mphs.marketplaceId 
            where 1=1 and   p.qty>0 and m.id=4 and phphmhs.isPublished=1  ".$marketplaceHasShopIdFind.$productBrandIdFind.$productCategoryIdFind;
            $countPages=\Monkey::app()->dbAdapter->query($sqlpagination,[])->fetchAll();
            foreach($countPages as $countPage) {
                $pageCount=$countPage['countItem'];
            }


        }

        $view->setTemplatePath($this->app->rootPath().$this->app->cfg()->fetch('paths', 'blueseal') . '/template/ebay_marketplacedisplaycatalog_product.php');

        $limit=100;

        $sql = "
            SELECT
              concat(phphmhs.productId, '-', phphmhs.productVariantId) AS productCode,
              phphmhs.productId,
              phphmhs.productVariantId,
              pps.price,
              (count( concat(phphmhs.productId, '-', phphmhs.productVariantId,'-',phphmhs.marketplaceHasShopId))/60) as countpage,     
              pb.name  as `brand`, 
              p.externalId AS externalId,
              phphmhs.price as marketplacePrice,
              phphmhs.salePrice as marketplaceSalePrice,
              phphmhs.lastTypeOperation as lastTypeOperation,phphmhs.refMarketplaceId as refMarketplaceId,      
              phphmhs.marketplaceHasShopId as marketplaceHasShopId,         
              concat(p.itemno, ' # ', pv.name)                                                              AS cpf, 
              phphmhs.titleModified as titleModified,     
              phphmhs.isOnSale AS isOnSale,
              p.qty as totalQty,
              PS.name as productStatus,   
               concat(p.itemno, ' # ', pv.name)                                                              AS title,     
              if(phphmhs.isOnSale=1,phphmhs.salePrice,phphmhs.price) as activePrice,    
              php.status,
              php.prestaId,
              phpc.productCategoryId as productCategoryId,
              psm.`name` as productStatusMarketplaceId,     
              mhs.imgMarketPlace as img,     
              if((isnull(p.dummyPicture) OR (p.dummyPicture = 'bs-dummy-16-9.png')), 'no', 'sì')            AS dummy,
               concat(shop.id, '-', shop.name)                                                                     AS shop,
               concat(pse.name, ' ', pse.year)                                                               AS season,
               psiz.name                                                                                             AS stock,
               mhs.name as marketplaceShopName    
            FROM PrestashopHasProductHasMarketplaceHasShop phphmhs join PrestashopHasProduct php ON php.productId = phphmhs.productId AND php.productVariantId = phphmhs.productVariantId and php.marketplaceHasShopId=phphmhs.marketplaceHasShopId
                join ProductStatusMarketplace psm on php.productStatusMarketplaceId=psm.id
            JOIN ProductPublicSku pps ON pps.productId = phphmhs.productId AND pps.productVariantId = phphmhs.productVariantId
             join ProductVariant pv on phphmhs.productVariantId=pv.id   
             JOIN Product p ON phphmhs.productId = p.id AND phphmhs.productVariantId = p.productVariantId    
             JOIN ProductStatus PS on p.productStatusId=PS.id       
             JOIN ProductBrand pb on p.productBrandId=pb.id
             JOIN MarketplaceHasShop mhs ON mhs.id = phphmhs.marketplaceHasShopId
             JOIN Shop s ON mhs.shopId = s.id
             join ProductHasProductCategory phpc on p.id=phpc.productId and p.productVariantId = phpc.productVariantId
             JOIN Marketplace m ON mhs.marketplaceId = m.id 
            JOIN ShopHasProduct sp
                    ON (p.id, p.productVariantId) = (sp.productId, sp.productVariantId)
                  JOIN Shop shop ON shop.id = sp.shopId
                   JOIN ProductSeason pse ON p.productSeasonId = pse.id
                   LEFT JOIN (ProductSku psk
                    JOIN ProductSize psiz ON psk.productSizeId = psiz.id)
                    ON (p.id, p.productVariantId) = (psk.productId, psk.productVariantId)
            where p.qty >0 and m.id=4 and phphmhs.isPublished=1 and phphmhs.refMarketplaceId is not null  ".$marketplaceHasShopIdFind.$productBrandIdFind.$productCategoryIdFind."
            GROUP BY phphmhs.productId, phphmhs.productVariantId,phphmhs.marketplaceHasShopId   limit  ".$limit." offset ".$offset;

        $productsFind=\Monkey::app()->dbAdapter->query($sql,[])->fetchAll();


        return $view->render([
            'app' => new CRestrictedAccessWidgetHelper($this->app),
            'page' => $this->page,
            'marketplaceAccount'=>$marketplaceAccount,
            'marketplaceHasShopId'=>$marketplaceHasShopId,
            'productBrandId'=>$productBrandId,
            'productCategoryId'=>$productCategoryId,
            'pageId'=>$pageId,
            'pageCount'=>$pageCount,
            'productsFind'=>$productsFind,
            'sidebar' => $this->sidebar->build()
        ]);
    }
}