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
class CProductPhotoAjaxManage extends AAjaxController
{
    public function get()
    {
        $imgSize = 562;

        $view = new VBase(array());
        $view->setTemplatePath($this->app->rootPath().$this->app->cfg()->fetch('paths', 'blueseal') . '/template/widgets/photos.php');
        $id = $this->app->router->request()->getRequestData('id');
        $productVariantId = $this->app->router->request()->getRequestData('productVariantId');
        $product = $this->app->repoFactory->create('Product')->findOne([$id, $productVariantId]);

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
        $product = $this->app->repoFactory->create('Product')->findOne([$this->app->router->request()->getRequestData('id'),
                                                                        $this->app->router->request()->getRequestData('productVariantId')]);
        $this->app->vendorLibraries->load("amazon2723");
        $config = $this->app->cfg()->fetch('miscellaneous', 'amazonConfiguration');
        $tempFolder = $this->app->rootPath().$this->app->cfg()->fetch('paths', 'tempFolder')."/";

        $image = new ImageManager(new S3Manager($config['credential']), $this->app, $tempFolder);

        if (!move_uploaded_file($_FILES['file']['tmp_name'], $tempFolder . $_FILES['file']['name'])) {
            throw new RedPandaException('Cannot move the uploaded Files');
        }


        $fileName['name'] = $product->printId();
        $fileName['number'] = (string) str_pad(rand(1000, 9999), 4, '0', STR_PAD_LEFT);
        $fileName['extension'] = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);

        try{
            $res = $image->process($_FILES['file']['name'], $fileName, $config['bucket'], $product->productBrand->slug);
        }catch(RedPandaAssetException $e){
            $this->app->router->response()->raiseProcessingError();
            return 'Dimensioni della foto errate: il rapporto deve esser 9:16';
        }

        $futureDummy = "";
        if (count($res) < 4) {
            //error
        } else {
            foreach ($res as $key => $val) {
                if (empty($futureDummy)) {
                    $futureDummy = $val;
                }
                $ids[] = $this->app->dbAdapter->insert('ProductPhoto', array('name' => $val, 'order' => $fileName['number'], 'size' => $key));
            }
            unlink($tempFolder . $_FILES['file']['name']);
            $count = 0;
            foreach ($ids as $key => $val) {
                $this->app->dbAdapter->insert("ProductHasProductPhoto", ["productId" => $product->id, "productVariantId" => $product->productVariantId, "productPhotoId" => $val]);
                $count++;
            }
            if ($count) {
                \Monkey::app()->eventManager->triggerEvent(
                    'assignPhotosToProduct',
                    [
                        'product' => $product,
                        'photoIds' => $ids,
                        'release' => 'release'
                    ]
                );
            }
        }

        return true;
    }

    public function put()
    {
        $data = $this->app->router->request()->getRequestData();
        $product = $this->app->repoFactory->create('Product')->findOne([$data['id'], $data['productVariantId']]);
        unset($data['id']);
        unset($data['productVariantId']);

        $this->app->dbAdapter->beginTransaction();
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
        $this->app->dbAdapter->commit();
        return true;
    }

    public function delete()
    {
        $id = $this->app->router->request()->getRequestData('id');
        $productVariantId = $this->app->router->request()->getRequestData('productVariantId');

        $product = $this->app->repoFactory->create('Product')->findOne([$id,$productVariantId]);

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
        $this->app->dbAdapter->beginTransaction();

        $this->app->vendorLibraries->load("amazon2723");
        $config = $this->app->cfg()->fetch('miscellaneous', 'amazonConfiguration');
        $s3 = new S3Manager($config['credential']);

        foreach($res as $photo){
            $del = $s3->delImage($product->productBrand->slug."/".$photo['name'],$config['bucket']);
            if(!$del) {
                $this->app->dbAdapter->rollback();
                throw new RedPandaException('Could not Delete all the photos');
            }
            $this->app->dbAdapter->delete('ProductHasProductPhoto',["productId"=>$id,"productVariantId"=>$productVariantId,"productPhotoId"=>$photo['id']]);
            $this->app->dbAdapter->delete('ProductHasProductPhoto',["productId"=>$id,"productVariantId"=>$productVariantId,"productPhotoId"=>$photo['id']]);
            $this->app->dbAdapter->delete('ProductPhoto',["id"=>$photo['id']]);
        }
        $this->app->dbAdapter->commit();
    }

}