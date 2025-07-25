<?php
namespace bamboo\controllers\back\ajax;

use bamboo\ecommerce\views\VBase;
use bamboo\core\exceptions\RedPandaAssetException;
use bamboo\core\exceptions\RedPandaException;
use bamboo\core\theming\CRestrictedAccessWidgetHelper;
use bamboo\core\utils\amazonPhotoManager\ImageManager;
use bamboo\core\utils\amazonPhotoManager\S3Manager;


/**
 * Class CProductEanUploadAjaxManage
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 28/10/2018
 * @since 1.0
 */
class CProductEanUploadAjaxManage extends AAjaxController
{


    public function post()
    {
        $tempFolder = $this->app->rootPath().$this->app->cfg()->fetch('paths', 'tempFolderCsv')."/";

        if (!move_uploaded_file($_FILES['file']['tmp_name'][0], $tempFolder . $_FILES['file']['name'][0])) {
            throw new RedPandaException('Cannot move the uploaded Files');
        }else{
            $filename = $tempFolder . $_FILES['file']['name'][0];

            $file = fopen( $filename, 'r');
            $count = 0;
            while (!feof($file)) {
                if (!$getData = fgetcsv($file, 100000, ';')) {
                    continue;
                }

                $insertEan=\Monkey::app()->repoFactory->create('ProductEan')->getEmptyEntity();
                $insertEan->ean=$getData[0];
                $insertEan->fileimported=$_FILES['file']['name'][0];
                $insertEan->insert();

            }
            fclose($file);


        }




        return true;
    }

    public function put()
    {
        $data = $this->app->router->request()->getRequestData();
        $product = \Monkey::app()->repoFactory->create('Product')->findOne([$data['id'], $data['productVariantId']]);
        unset($data['id']);
        unset($data['productVariantId']);

        \Monkey::app()->repoFactory->beginTransaction();
	    foreach ($product->productPhoto as $photo) {
		    foreach ($data as $newOrder => $oldOrder) {
			    $oldOrder = (int)substr($oldOrder, 5);
			    $newOrder = ((int)substr($newOrder, 1)) + 1;
			    if ($oldOrder != $photo->order || $oldOrder == $newOrder ) continue;
			    $photo->order = $newOrder;
			    $photo->update();
			    break;
		    }
	    }
        \Monkey::app()->repoFactory->commit();
        return true;
    }

    public function delete()
    {
        $id = $this->app->router->request()->getRequestData('id');
        $productVariantId = $this->app->router->request()->getRequestData('productVariantId');

        $product = \Monkey::app()->repoFactory->create('Product')->findOne([$id,$productVariantId]);

        $res = $this->app->dbAdapter->query("SELECT pp.id, pp.name
                                      FROM  ProductHasProductPhoto ppp,
                                            ProductPhoto pp
                                      WHERE ppp.productPhotoId = pp.id AND
                                            ppp.productId = ? AND
                                            ppp.productVariantId = ? AND
                                            pp.`order` = ?",[
                                                        $id,
                                                        $productVariantId,
                                                        substr($this->app->router->request()->getRequestData('photoOrder'),5)])->fetchAll();
        \Monkey::app()->repoFactory->beginTransaction();

        $this->app->vendorLibraries->load("amazon2723");
        $config = $this->app->cfg()->fetch('miscellaneous', 'amazonConfiguration');
        $s3 = new S3Manager($config['credential']);

        foreach($res as $photo){
            $del = $s3->delImage($product->productBrand->slug."/".$photo['name'],$config['bucket']);
            if(!$del) {
                \Monkey::app()->repoFactory->rollback();
                throw new RedPandaException('Could not Delete all the photos');
            }
            $this->app->dbAdapter->delete('ProductHasProductPhoto',["productId"=>$id,"productVariantId"=>$productVariantId,"productPhotoId"=>$photo['id']]);
            $this->app->dbAdapter->delete('ProductHasProductPhoto',["productId"=>$id,"productVariantId"=>$productVariantId,"productPhotoId"=>$photo['id']]);
            $this->app->dbAdapter->delete('ProductPhoto',["id"=>$photo['id']]);
        }
        \Monkey::app()->repoFactory->commit();
    }

}