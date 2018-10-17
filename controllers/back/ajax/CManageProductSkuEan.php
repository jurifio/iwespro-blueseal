<?php

namespace bamboo\controllers\back\ajax;
use bamboo\core\base\CObjectCollection;
use bamboo\domain\entities\CProduct;
use bamboo\domain\entities\CProductSizeGroup;
use bamboo\domain\entities\CProductSku;
use bamboo\domain\repositories\CProductRepo;
use bamboo\domain\repositories\CProductSizeRepo;
use bamboo\domain\repositories\CProductSkuRepo;

/**
 * Class CManageProductSkuEan
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 02/08/2018
 * @since 1.0
 */
class CManageProductSkuEan extends AAjaxController
{
  public function get(){

    $productId = \Monkey::app()->router->request()->getRequestData('p');

    $pId = explode('-', $productId)[0];
    $pVId = explode('-', $productId)[1];

    /** @var CObjectCollection $psC */
    $psC = \Monkey::app()->repoFactory->create('ProductSku')->findBy(['productId'=>$pId, 'productVariantId'=>$pVId]);

    $r = [];
    $c = 0;
    /** @var CProductSku $ps */
    foreach ($psC as $ps){
        $r[$c]['sizeId'] = $ps->productSizeId;
        $r[$c]['sizeName'] = $ps->productSize->name;
        $r[$c]['ean'] = $ps->ean;
        $c++;
    }

    return json_encode($r);
  }


  public function post(){
      $p = \Monkey::app()->router->request()->getRequestData('p');
      $sizes = \Monkey::app()->router->request()->getRequestData('size');

      $pId = explode('-', $p)[0];
      $pVId = explode('-', $p)[1];

      /** @var CProductSkuRepo $pskRepo */
      $pskRepo = \Monkey::app()->repoFactory->create('ProductSku');

      foreach ($sizes as $key=>$val){
          /** @var CProductSku $psk */
          $psk = $pskRepo->findOneBy(['productId'=>$pId, 'productVariantId'=>$pVId, 'productSizeId'=>$val['size']]);
          $psk->ean = $val["val"];
          $psk->update();
      }

      return "EAN inseriti";
  }
}