<?php

namespace bamboo\blueseal\jobs;


use bamboo\core\exceptions\RedPandaAssetException;
use bamboo\domain\entities\COrder;
use bamboo\domain\entities\CCart;

use bamboo\domain\entities\CCouponType;
use bamboo\domain\entities\CCoupon;
use bamboo\domain\entities\CCartLine;
use bamboo\core\base\CSerialNumber;
use bamboo\core\db\pandaorm\repositories\ARepo;
use bamboo\domain\repositories\CEmailRepo;
use PDO;
use prepare;
use bamboo\core\utils\amazonPhotoManager\ImageManager;
use bamboo\core\utils\amazonPhotoManager\S3Manager;

use bamboo\core\jobs\ACronJob;
use bamboo\domain\entities\CProductPublicSku;
use bamboo\domain\entities\CProduct;
use bamboo\core\events\AEventListener;


/**
 * Class CUpdateCouponValidityJob
 * @package bamboo\blueseal\jobs
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 23/12/2019
 * @since 1.0
 */
class CRenameImageJpegS3Job extends ACronJob
{
    /**
     * @param null $args
     * @throws \bamboo\core\exceptions\BambooDBALException
     */
    public function run($args = null)

    {
        try {
            $this->app->vendorLibraries->load("amazon2723");

            $config = $this->app->cfg()->fetch('miscellaneous','amazonConfiguration');
            $s3 = new S3Manager($config['credential']);
            $sql = "SELECT p.id,p.productVariantId,phs.productPhotoId,pb.slug as slug, pp.`name` as `name`,pp.id as photoId  FROM  ProductPhoto pp JOIN ProductHasProductPhoto phs ON pp.id=phs.productPhotoId JOIN Product p ON phs.productId=p.id AND phs.productVariantId=p.productVariantId
join ProductBrand pb ON p.productBrandId=pb.id ";
            $res = \Monkey::app()->dbAdapter->query($sql,[])->fetchAll();
            \Monkey::app()->applicationLog('CRenameImageJpegS3Job','Report','startLoop','','');
            foreach ($res as $result) {
                $transitionName= str_replace('.jpg','.JPG',$result['name']);
                $url='https://cdn.iwes.it/'.$result['slug'].'/'.$transitionName;
                if(@getimagesize($url)) {
                    $oldName = $result['name'];
                    $newName = str_replace('.JPG','.jpg',$result['name']);
                    $image = new ImageManager(new S3Manager($config['credential']),$this->app,"");
                    $image->copy($result['slug'] . '/' . $oldName,$config['bucket'],$result['slug'] . '/' . $newName,$config['bucket']);
                    $s3->delImage($result['slug'] . '/' . $oldName,$config['bucket']);
                    \Monkey::app()->applicationLog('CRenameImageJpegS3Job','Report','productPhoto rename',$url,'');
                }else{
                    \Monkey::app()->applicationLog('CRenameImageJpegS3Job','Report','productPhoto saltata',$url,'');
                    continue;
                }
            }
        }catch (\Throwable $e) {
            \Monkey::app()->applicationLog('CRenameImageJpegS3Job','error','productPhoto rename',$e->getMessage(),$e->getLine());
        }

    }


}