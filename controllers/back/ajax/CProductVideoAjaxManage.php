<?php
namespace bamboo\controllers\back\ajax;

use bamboo\ecommerce\views\VBase;
use bamboo\core\exceptions\RedPandaAssetException;
use bamboo\core\exceptions\RedPandaException;
use bamboo\core\theming\CRestrictedAccessWidgetHelper;
use bamboo\core\utils\amazonPhotoManager\ImageManager;
use bamboo\core\utils\amazonPhotoManager\S3Manager;

/**
 * Class CProductPhotoAjaxManage
 * @package bamboo\blueseal\controllers\ajax
 * @author Bambooshoot Team <emanuele@bambooshoot.agency>, ${DATE}
 * @copyright (c) Bambooshoot snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @since ${VERSION}
 */
class CProductVideoAjaxManage extends AAjaxController
{
    public function get()
    {
        $imgSize = 562;

        $view = new VBase(array());
        $view->setTemplatePath($this->app->rootPath().$this->app->cfg()->fetch('paths', 'blueseal') . '/template/widgets/videos.php');
        $id = $this->app->router->request()->getRequestData('id');
        $productVariantId = $this->app->router->request()->getRequestData('productVariantId');
        $product = \Monkey::app()->repoFactory->create('Product')->findOne([$id, $productVariantId]);

        $photos = $this->app->dbAdapter->query("SELECT distinct `name`, `order`,size
                                      FROM ProductPhoto pp, ProductHasProductPhoto ppp
                                      WHERE pp.id = ppp.productPhotoId AND
                                      ppp.productId = ? AND
                                      ppp.productVariantId = ? AND
                                      pp.size = ?
                                      GROUP BY pp.id
                                      ORDER BY pp.order", [$product->id, $product->productVariantId, $imgSize])->fetchAll();

        return $view->render([
            'app' => new CRestrictedAccessWidgetHelper($this->app),
            'photos' => $photos,
            'product' => $product
        ]);
    }

    public function post()
    {
        $product = \Monkey::app()->repoFactory->create('Product')->findOne([$this->app->router->request()->getRequestData('id'),
            $this->app->router->request()->getRequestData('productVariantId')]);
        $this->app->vendorLibraries->load("amazon2723");
        $config = $this->app->cfg()->fetch('miscellaneous', 'amazonConfiguration');
        $tempFolder = $this->app->rootPath().$this->app->cfg()->fetch('paths', 'tempFolder')."-blog/";

        $image = new ImageManager(new S3Manager($config['credential']), $this->app, $tempFolder);
       $stringFile= str_replace('jpg','mp4',$_FILES['file']['name']);
        if (!move_uploaded_file($_FILES['file']['tmp_name'], $tempFolder . $_FILES['file']['name'])) {
            throw new RedPandaException('Cannot move the uploaded Files');
        }


        $fileName['name'] = $product->printId();
        $fileName['number'] = (string) str_pad(rand(1000, 9999), 4, '0', STR_PAD_LEFT);
        $fileName['extension'] = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
        $fileProduct=$fileName['name'].'.'.$fileName['extension'];
        try{
            $res = $image->processVideoUploadProduct($_FILES['file']['name'], $fileName, $config['bucket'], $product->productBrand->slug);
        }catch(RedPandaAssetException $e){
            $this->app->router->response()->raiseProcessingError();
            return 'Dimensioni della foto errate: il rapporto deve esser 9:16';
        }
        $product->dummyVideo=$fileProduct;
        $product->update();


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