<?php

namespace bamboo\controllers\back\ajax;
use bamboo\core\exceptions\BambooConfigException;

/**
 * Class CAssignEanToMarketPlaceProductAssociate
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 22/12/2018
 * @since 1.0
 */
class CAssignEanToMarketPlaceProductAssociate extends AAjaxController
{
    public function post()
    {

	    $count=0;
	    $productsCount=0;
    	$products = $this->app->router->request()->getRequestData('rows');
	    foreach ($products as $product) {

            $array=array($product);
            $arrayproduct=implode('-',$array);

            $singleproduct=explode('-',$arrayproduct);
            $productId=$singleproduct[0];
            $productVariantId=$singleproduct[1];
//assegnazione a prodotto Parente
	    	$productMarketPlaceHasProductAssociate = \Monkey::app()->repoFactory->create('MarketPlaceHasProductAssociate')->findOneBy(['productId'=>$productId,'productVariantId'=>$productVariantId]);
		    //$shopId=$productMarketPlaceHasProductAssociate->shopId;

		     $brandId=\Monkey::app()->repoFactory->create('Product')->findOneBy(['id'=>$productId,'productVariantId'=>$productVariantId]);
		    $brandAssociate=$brandId->productBrandId;
		    $eanFree=\Monkey::app()->repoFactory->create('ProductEan')->findOneBy(['used'=> 0]);
		    $eanFree->productId=$productId;
		    $eanFree->productVariantId=$productVariantId;
		    $eanFree->productSizeId=0;
		    $eanFree->usedForParent=1;
		    $eanFree->used=1;
		    $eanFree->brandAssociate=$brandAssociate;
		    $eanFree->shopId=1;
		    $eanFree->update();
		    $productsCount=$productsCount+1;

		    $productSku=\Monkey::app()->repoFactory->create('ProductSku')->findOneBy(['productId'=>$productId,'productVariantId'=>$productVariantId]);
		     foreach ($productSku as $productSkus){
					if(empty($productSkus->ean)){
                        $eanFreeSkus=\Monkey::app()->repoFactory->create('ProductEan')->findOneBy(['used'=> 0]);
                        $eanforVariant=$eanFreeSkus->ean;
                        $eanFreeSkus->productId=$productSkus->productId;
                        $eanFreeSkus->productVariantId=$productSkus->productVariantId;
                        $eanFreeSkus->productSizeId=$productSkus->productSizeId;
                        $eanFreeSkus->used=1;
                        $eanFreeSkus->brandAssociate=$brandAssociate;
                        $eanFreeSkus->shopId=1;
                        $eanFreeSkus->update();
                        $productSkus->ean=$eanforVariant;
                        $productSkus->update();
                        $count=$count+1;
                        $count=$count+1;
			    }
		    }

	    }
	    return json_encode(['products'=>$productsCount,'skus'=>$count]);
    }
}